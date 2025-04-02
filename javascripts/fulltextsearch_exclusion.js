const appConfig = {
  props: {
    state: Boolean,
    disabled: Boolean,
    tag: String
  },
  methods: {
    toggle: function () {
      this.disabled = true;
      fetch(wiki.url('?api/fulltextsearch/admin/exclusions/toggle'), {
        method: 'POST',
        body: JSON.stringify({
          tag: this.tag,
        }),
      })
        .then(res => res.json())
        .then(res => {
          this.state = res.newState;
          this.disabled = false;
        })
      ;
    },
  },
  computed: {
    t: () => ({
      FULLTEXTSEARCH_EXCLUSIONS_EXCLUDED: _t('FULLTEXTSEARCH_EXCLUSIONS_EXCLUDED'),
      FULLTEXTSEARCH_EXCLUSIONS_INDEXED: _t('FULLTEXTSEARCH_EXCLUSIONS_INDEXED'),
    })
  },
  template: `
    <div>
      <template v-if="state === true">
        <button 
          class="btn btn-danger btn-xs"
          :disabled="disabled"
          v-on:click="toggle"
        >{{ t.FULLTEXTSEARCH_EXCLUSIONS_EXCLUDED}} </button>
      </template>
      <template v-else>
        <button 
          class="btn btn-success btn-xs"
          :disabled="disabled"
          v-on:click="toggle"
        >{{ t.FULLTEXTSEARCH_EXCLUSIONS_INDEXED}}</button>
      </template>
    </div>
  `
};

const initButton = () => {
  const buttonElts = document.querySelectorAll('.fullTextSearch-exclusion');
  if(buttonElts.length === 0) {
    return;
  }

  for(const buttonElt of buttonElts) {
    new Vue({
      el: buttonElt,
      propsData: {
        state: buttonElt.getAttribute('data-initial-state') === '1',
        tag: buttonElt.getAttribute('data-tag'),
        disabled: false
      },
      ...appConfig
    });
  }
};
initButton();
