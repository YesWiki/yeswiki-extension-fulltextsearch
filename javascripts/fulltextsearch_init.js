const STATE_INITIIALIZED = 'initialized';
const STATE_UNINITIIALIZED = 'uninitialized';
const STATE_INITIALIZING = 'initializing';

import initProcess from './components/initProcess.js';

const appConfig = {
  components: {
    'init-process': initProcess
  },
  props: {
    state: String
  },
  methods: {
    init: function () {
      this.state = STATE_INITIALIZING;
    },
    initCompleted: function (errorMessage) {
      if(errorMessage === null) {
        toastMessage(this.t.FULLTEXTSEARCH_INIT_BUTTON_SUCCESS, 3000, 'alert alert-success');
      } else {
        toastMessage(errorMessage, 3000, 'alert alert-danger');
      }

      this.state = STATE_INITIIALIZED;
    }
  },
  computed: {
    t: () => ({
      FULLTEXTSEARCH_INIT_BUTTON_INITIALIZED: _t('FULLTEXTSEARCH_INIT_BUTTON_INITIALIZED'),
      FULLTEXTSEARCH_INIT_BUTTON_TO_INITIALIZE: _t('FULLTEXTSEARCH_INIT_BUTTON_TO_INITIALIZE'),
      FULLTEXTSEARCH_INIT_BUTTON_SUCCESS: _t('FULLTEXTSEARCH_INIT_BUTTON_SUCCESS')
    })
  },
  template: `
    <div id="fullTextSearch-engineConfigure">
      <template v-if="state === '${STATE_INITIIALIZED}'">
        <button class="btn btn-primary" v-on:click="init">{{ t.FULLTEXTSEARCH_INIT_BUTTON_INITIALIZED }}</button>
      </template>
      <template v-else-if="state === '${STATE_UNINITIIALIZED}'">
        <button class="btn btn-primary" v-on:click="init">{{ t.FULLTEXTSEARCH_INIT_BUTTON_TO_INITIALIZE }}</button>
      </template>
      <template v-else-if="state === '${STATE_INITIALIZING}'">
        <init-process v-on:init-completed="initCompleted"></init-process>
      </template>
    </div>
  `
};

const initButton = () => {
  const buttonElt = document.querySelector('#fullTextSearch-engineConfigure');
  if(buttonElt === null) {
    return;
  }

  new Vue({
    el: buttonElt,
    propsData: {
      state: buttonElt.getAttribute('data-initial-state')
    },
    ...appConfig
  })
};
initButton();
