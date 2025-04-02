export default {
  data: () => ({
    currentOffset: 0,
    pageCount: -1,
    progress: 0,
  }),
  methods: {
    updatePageCount(newValue) {
      this.pageCount = newValue;
    },
    updateCurrentOffset(newValue) {
      this.currentOffset = newValue;
      this.progress = ((newValue / this.pageCount) * 100).toFixed(2);
    }
  },
  mounted() {
    startProcess(this);
  },
  template: `
  <button class="btn btn-primary" disabled>
    <i class="fas fa-circle-notch fa-spin"></i>
    <span v-if="pageCount > 0">
      {{ progress }}%
    </span>
  </button>
  `,
}

const startProcess = async (component) => {
  const total = await reqGetTotal();
  component.updatePageCount(total);

  let offset = 0;
  while (offset <= total) {
    component.updateCurrentOffset(offset);
    try {
      offset = await reqPostInit(offset);
    } catch (e) {
      component.$emit('init-completed', e.message);
    }

  }
  component.$emit('init-completed', null);
}

const reqGetTotal = async () => {
  const res = await fetch(wiki.url('api/fulltextsearch/admin/total'));
  if (!res.ok) {
    throw new Error('Failed to fetch total');
  }
  const data = await res.json();
  return data.total;
}

const reqPostInit = async (offset) => {
  const res = await fetch(wiki.url('api/fulltextsearch/admin/init'), {
    method: 'POST',
    body: JSON.stringify({
      offset: offset,
    }),
  });
  if (!res.ok) {
    let errorMessage = null;
    try {
      errorMessage = await parseErrorMessageFromResponse(res);
    } catch (e) {
      throw new Error(_t('FULLTEXTSEARCH_ERROR_UNKNOWN'));
    }

    throw new Error(errorMessage);
  }

  const resJson = await res.json();
  return resJson.nextOffset;
}

const parseErrorMessageFromResponse = async (res) => {
  const error = await res.json();
  return error.exceptionMessage;
}
