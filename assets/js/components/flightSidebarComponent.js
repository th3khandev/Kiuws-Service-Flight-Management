const flightSidebarComponent = {
  template: `
    <div>
      Sidebar filters
    </div>
  `,
  data: () => ({
    massage: 'este es el sidebar',
  }),
  props: {
    loading: Boolean,
  },
  methods: {
  },
};

export default flightSidebarComponent;