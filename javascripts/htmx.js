function addToastMessageFromHtmxHeaderResponse(messages) {
    for(message of messages) {
      let arguments = [message.message];
      if (message.duration) {
        arguments.push(message.duration);
      }
      if (message.toastClass) {
        arguments.push(message.toastClass);
      }

      toastMessage(...arguments);
    }
}

/**
 * Display toast notifications based on the X-Toast-Message header in HTMX responses.
 */
document.addEventListener('htmx:afterRequest', function(event) {
    const response = event.detail.xhr.getResponseHeader('X-Toast-Message');
    if (!response) {
        return;
    }

    const responseDecoded = JSON.parse(response);
    addToastMessageFromHtmxHeaderResponse(responseDecoded);
});

/**
 * Display toast notifications for errors in HTMX requests.
 */
document.addEventListener('htmx:beforeRequest', function(event) {
  event.detail.requestConfig.requestStartedAt = Date.now(); // Used for timeout detection
});

document.addEventListener('htmx:afterRequest', function(event) {
  if (!event.detail.failed) {
    return;
  }

  const parseErrorFromResponse = (event) => {
    const requestEndedAt = Date.now();
    const duration = requestEndedAt - event.detail.requestConfig.requestStartedAt;
    const TIMEOUT_THRESHOLD = 5*1000; // We assume that errors occurring after threshold are timeouts
    if(duration > TIMEOUT_THRESHOLD) {
      return _t('FULLTEXTSEARCH_ERROR_TIMEOUT');
    }


    try {
      const parsed = JSON.parse(event.detail.xhr.response);
      return parsed.exceptionMessage;
    } catch (e) {

    }
    return _t('FULLTEXTSEARCH_ERROR_UNKNOWN')
  }

  toastMessage(
    parseErrorFromResponse(event),
    20000 // Increate duration for error messages as they might need more time to read
  );
});
