<div class="leftbar-container align-self-stretch" v-bind:class="{ 'open-sidebar': leftOpen }" :style="{
            'min-height': lbh+'px'
     }">
    <a href="javascript:void(0);" class="arrow-sidebar d-block d-md-none" @click="togglesidebar">
        <i class="fa fa-lg" v-bind:class="{ 'fa-arrow-left': leftOpen, 'fa-arrow-right': !leftOpen }"></i>
    </a>
    <div class="py-4">
        <ul class="ul-lsn list-sidebar">
            <li class="{{ (request()->is('client/lead')) ? 'active' : '' }}">
                <a href="{{url("/client/lead/")}}"><i class="fa fa-list"></i> Leads</a>
            </li>
            <li class="{{ (request()->is('client/funnel')) ? 'active' : '' }}">
                <a href="{{url("/client/funnel/")}}"><i class="fa fa-file-code-o"></i> Funnels</a>
            </li>
            <li class="{{ (request()->is('client/emailmanager')) ? 'active' : '' }}">
                <a href="{{url("/client/emailmanager/")}}"><i class="fa fa-paper-plane"></i> Email Manager</a>
            </li>
        </ul>
    </div>
</div>
