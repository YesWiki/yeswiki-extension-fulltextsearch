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

document.addEventListener('htmx:afterRequest', function(event) {
    const response = event.detail.xhr.getResponseHeader('X-Toast-Message');
    if (!response) {
        return;
    }

    const responseDecoded = JSON.parse(response);
    addToastMessageFromHtmxHeaderResponse(responseDecoded);
});

document.addEventListener('htmx:afterRequest', function(event) {
  if (!event.detail.failed) {
    return;
  }

  const parseErrorFromResponse = (event) => {
    try {
      const parsed = JSON.parse(event.detail.xhr.response);
      return parsed.exceptionMessage;
    } catch (e) {

    }
    return _t('FULLTEXTSEARCH_ERROR_UNKNOWN')
  }

  toastMessage(parseErrorFromResponse(event));
});
