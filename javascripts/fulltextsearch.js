function debounce(func, timeout = 300){
  let timer;
  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => { func.apply(this, args); }, timeout);
  };
}

function getSearchWrapper() {
  return $('#fullTextSearch_searchwrapper');
}
function getCurrentSearchQuery() {
  const url = new URL(window.location.href);
  return decodeURIComponent(url.searchParams.get('fullTextSearch_search') ?? '');
}

const CLASS_LOADING = 'loading';
function fulltextsearch() {
  const searchQuery = getCurrentSearchQuery();
  if(searchQuery === '') {
    $('#fullTextSearch_searchresult').html('');
    getSearchWrapper().removeClass(CLASS_LOADING)
    return;
  }

  fetch(wiki.url('?api/fulltextsearch/search'), {
    method: 'POST',
    body: JSON.stringify({
      query: searchQuery,
    }),
  })
    .then(response => {
      if (!response.ok) {
        return `<div class="alert alert-danger">${_t('FULLTEXTSEARCH_ERROR_UNKNOWN')}</div>`;
      }
      return response.text();
    })
    .then(data => {
      $('#fullTextSearch_searchresult').html(data);
      getSearchWrapper().removeClass(CLASS_LOADING)
    })
}

$(document).ready(() => {
  $('#fullTextSearch_searchwrapper [name="fullTextSearch_search"]')
    // Bind events
    .on('keyup', debounce(fulltextsearch, 500))
    .on('keyup', () => {getSearchWrapper().addClass(CLASS_LOADING)})
    .on('keyup', (e) => {
      let url = new URL(window.location.href);
      url.searchParams.set('fullTextSearch_search', encodeURIComponent(e.target.value));
      window.history.replaceState({}, '', url);
    })

    // Set initial value
    .val(getCurrentSearchQuery())
    .trigger('keyup')
  ;

});
