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
const CLASS_LOADING = 'loading';
function fulltextsearch(e) {
  fetch(wiki.url('?api/fulltextsearch/search'), {
    method: 'POST',
    body: JSON.stringify({
      query: e.target.value,
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
    .on('keyup', debounce(fulltextsearch, 500))
    .on('keyup', () => {getSearchWrapper().addClass(CLASS_LOADING)})
  ;
});
