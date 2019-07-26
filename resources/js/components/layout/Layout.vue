<script>

    var Sidebar = require('./Sidebar.vue');
    var Header = require('./Header.vue');

    var layout1_mixin = [Sidebar.default, Header.default];
    var layout2_mixin = [Sidebar.default];

    var mixin_arr = [];
    if(pageLayout === 'layout1') {
        mixin_arr = layout1_mixin;
    }
    if(pageLayout === 'layout2') {
        mixin_arr = layout2_mixin;
    }

    export default {
        mixins: mixin_arr,
    }

</script>
