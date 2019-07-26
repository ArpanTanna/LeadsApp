<script>
    export default {
        data: () => ({
            leftOpen: 0
        }),
        methods: {
            togglesidebar() {
                this.leftOpen = !this.leftOpen
            }
        },
        mounted() {
            //console.log('Sidebar Loaded');
        }
    }
</script>
