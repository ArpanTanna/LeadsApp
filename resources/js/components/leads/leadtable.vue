<template>
    <div class="clearfix mt-4">
        <div class="table-info-pre clearfix mb-2">
            <div class="row">
                <div class="col-md-6">
                    <p>Total Records: <span class="badge badge-primary">{{items.length}}</span> </p>
                </div>
                <div class="col-md-6 text-right">
                    <b-button @click="emailmanager" variant="primary">Send to Email Manager</b-button>
                </div>
            </div>
        </div>
        <div class="clearfix loader-parent" :class="{'loading': isLoader}">
            <b-table class="ttype1" ref="tableLead" striped hover responsive :busy="isBusy"
                     selectable select-mode="multi" @row-selected="rowSelected"
                     no-local-sorting no-sort-reset @sort-changed="sortingChanged" :sort-by.sync="mySortBy" :sort-desc.sync="mySortDesc"
                     :items="itemsFiltered" :fields="fields">
                <template slot="HEAD_col1" slot-scope="data">
                    <a href="javascript:void(0);"><i class="fa fa-cog"></i></a>
                </template>
                <template slot="col1" slot-scope="data">
                    <a href="javascript:void(0);" :id="'pop-'+data.item.id"><i class="fa fa-bars"></i></a>
                    <b-popover
                        :target="'pop-'+data.item.id"
                        placement="rightbottom"
                        triggers="focus"
                    >
                        <template slot="title">Actions</template>
                        <div>
                            <p class="m-0 mb-1"><a href="javascript:void(0);" @click="infoInit(data.item)">Info</a></p>
                            <p class="m-0 mb-1"><a href="javascript:void(0);" @click="editInit(data.item)">Edit</a></p>
                            <p class="m-0"><a href="javascript:void(0);" @click="deleteInit(data.item, data.index)">Delete</a></p>
                        </div>
                    </b-popover>
                </template>
                <template slot="title" slot-scope="data">
                    {{ data.item.other_info.title }}
                </template>
                <template slot="company_name" slot-scope="data">
                    {{ data.item.company_name }}
                </template>
                <template slot="city" slot-scope="data">
                    {{ data.item.other_info.city }}
                </template>
                <template slot="status" slot-scope="data">
                    <div class="status-box">
                        <div class="progress">
                            <div class="progress-bar bg-normal" v-bind:class="{'bg-primary': data.item.status>=0}" role="progressbar" data-toggle="tooltip" :title="statusList[0]" @click="chgStatus(0, data.item, $event)"></div>
                            <div class="progress-bar bg-normal" v-bind:class="{'bg-primary': data.item.status>=1}" role="progressbar" data-toggle="tooltip" :title="statusList[1]" @click="chgStatus(1, data.item, $event)"></div>
                            <div class="progress-bar bg-normal" v-bind:class="{'bg-primary': data.item.status>=2}" role="progressbar" data-toggle="tooltip" :title="statusList[2]" @click="chgStatus(2, data.item, $event)"></div>
                            <div class="progress-bar bg-normal" v-bind:class="{'bg-primary': data.item.status>=3}" role="progressbar" data-toggle="tooltip" :title="statusList[3]" @click="chgStatus(3, data.item, $event)"></div>
                        </div>
                        <small>{{statusList[data.item.status]}}</small>
                    </div>
                </template>
                <template slot="name" slot-scope="data">
                    {{ data.item.first_name }} {{ data.item.last_name }}
                </template>
                <template slot="contact" slot-scope="data">
                    <span class="d-none trkey" :data-trkey="data.item.ukey"></span>
                    <a v-bind:href="'mailto:'+data.item.email" class="btn btn-primary btn-sm" v-if="data.item.email"><i class="fa fa-envelope"></i> </a>
                    <a v-bind:href="'tel:'+data.item.phone" class="btn btn-primary btn-sm" v-if="data.item.phone"><i class="fa fa-phone"></i> </a>
                </template>
                <div slot="table-busy" class="text-center text-t1 my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Loading...</strong>
                </div>
            </b-table>
            <div class="clearfix text-center mt-3 mb-5" v-if="isrc == 0">
                <a href="javascript:void(0);" class="btn btn-primary" @click="loadMore">Load More</a>
            </div>
        </div>
        <p>&nbsp;</p>
    </div>
</template>

<script>
    export default {
        name: 'lead-table',
        props: [
            'modalUpdatedData'
        ],
        data: () => ({
            fields: [
                {'key': 'col1', 'sortable': false},
                {'key': 'title', 'sortable': true},
                {'key': 'status', 'sortable': false},
                {'key': 'name', 'sortable': true},
                {'key': 'company_name', 'sortable': true},
                {'key': 'city', 'sortable': true},
                {'key': 'contact', 'sortable': false}
            ],
            items: [],
            leadSelected: [],
            isBusy: false,
            mySortBy: '',
            mySortDesc: false,
            sort: {},
            currentPage: 0,
            isrc: 0,
            statusList: {0:'Unassigned', 1:'In Progress', 2:'Processed', 3: 'Completed'},
            isLoader: 0
        }),
        watch: {
            modalUpdatedData: function(newVal, oldVal) {
                if(newVal.type === 'update') {
                    Vue.set(this.items, this.items.findIndex(f => f.id === newVal.data.id), newVal.data)
                }
                else {
                    this.items.unshift(newVal.data);
                }
            }
        },
        computed: {
            itemsFiltered() {
                var filtered_array = [];
                var ids = [];
                this.items.forEach(function(value, key) {
                    if(!ids.includes(value.id)) {
                        filtered_array.push(value);
                        ids.push(value.id);
                    }
                });

                return filtered_array;
            }
        },
        methods: {
            toggleBusy() {
                this.isBusy = !this.isBusy
            },

            /*
             * Fetch records from backend
             * Use $http method (instead of axios)
             */
            fetchRecords() {
                this.isLoader = 1;
                this.$http.post(
                    '/client/lead/apiaccess/fetchrecords',
                    {'currentPage': this.currentPage, 'sortBy': this.sort.by, 'sort': this.sort.type}
                )
                    .then(function(responseF)
                        {
                            let response = responseF.body;
                            if(response.status == 1) {
                                //this.items = response.items;
                                this.items.push.apply(this.items, response.items);
                                this.currentPage = this.currentPage + 1;
                                if(response.items.length < response.limit) {
                                    this.isrc = 1;
                                }
                            }
                            else {
                                this.$emit('alertTrigger', {'type': 2, 'message': response.message});
                            }
                            this.isLoader = 0;
                        }, function() {
                            this.$emit('alertTrigger', {'type': 2, 'message': 'An error occurred.'});
                            this.isLoader = 0;
                        }
                    );
            },

            loadMore() {
                this.fetchRecords();
            },

            sortingChanged(ctx) {
                if(ctx.sortBy && ctx.sortBy !== null) {
                    this.sort.by = ctx.sortBy;
                    this.sort.type = (ctx.sortDesc === false) ? 'desc' : 'asc';

                    this.items = [];
                    this.currentPage = 0;
                    this.isrc = 0;
                    this.fetchRecords();
                }
            },

            /*
             * Change status of lead
             * Use $http method (instead of axios)
             */
            chgStatus(nStatus, item, event) {
                if(item.status != nStatus) {
                    var $this = this;
                    this.$http.post(
                        '/client/lead/apiaccess/chgstatus',
                        {'status': nStatus, 'ukey': item.ukey}
                    )
                    .then(function(responseF)
                        {
                            var response = responseF.body;
                            if(response.status == 1) {
                                Vue.set($this.items, $this.items.findIndex(f => f.ukey === item.ukey), response.record);
                            }
                            else {
                                this.$emit('alertTrigger', {'type': 2, 'message': response.message});
                            }
                        }, function() {
                            this.$emit('alertTrigger', {'type': 2, 'message': 'An error occurred while change status'});
                        }
                    );
                }
            },

            infoInit(data) {
                this.$emit('tableTriggerFrom', {'type': 'infoModal', 'data': data});
            },

            editInit(data) {
                this.$emit('tableTriggerFrom', {'type': 'edit', 'data': data});
            },

            deleteInit(data, index) {
                if(confirm("Do you really want to delete this lead?"))
                {
                    let $this = this;
                    $this.isLoader = 1;

                    axios.delete('/client/lead/'+data.ukey, {})
                        .then((response) => {
                            if(response.data.status == 1) {
                                // Delete row
                                let itemIndex = $this.items.findIndex(f => f.id === data.id);
                                $this.items.splice(itemIndex, 1);
                                $this.$emit('alertTrigger', {'type': 1, 'message': response.data.message});
                            }
                            else {
                                $this.$emit('alertTrigger', {'type': 2, 'message': response.data.message});
                            }
                            $this.isLoader = 0;
                        })
                        .catch((error) => {
                            console.log(error.response);
                            $this.$emit('alertTrigger', {'type': 2, 'message': 'An error occurred.'});
                            $this.isLoader = 0;
                        });
                }
            },

            /*
             * Get selected lead ids
             */
            rowSelected(items) {
                let selectedIds = [];
                items.map(function (item) {
                    selectedIds.push(item.id);
                });
                this.leadSelected = selectedIds;
            },

            /*
             * Selected rows save to DB
             * And send to emailmanager page
             */
            emailmanager() {
                if(this.leadSelected.length > 0) {
                    this.isLoader = 1;
                    this.$http.post(
                        '/client/lead/apiaccess/emailmanager',
                        {'ids': this.leadSelected}
                    )
                        .then(function(responseF)
                            {
                                let response = responseF.body;
                                if(response.status == 1) {
                                    this.$emit('alertTrigger', {'type': 1, 'message': response.message});
                                    window.location.href = '/client/emailmanager';
                                }
                                else {
                                    this.$emit('alertTrigger', {'type': 2, 'message': response.message});
                                }
                                this.isLoader = 0;
                            }, function() {
                                this.$emit('alertTrigger', {'type': 2, 'message': 'An error occurred.'});
                                this.isLoader = 0;
                            }
                        );
                }
                else {
                    this.$emit('alertTrigger', {'type': 2, 'message': 'Please select at least one lead.'});
                }
            }
        },
        mounted() {
            $('body').tooltip({
                selector: '[data-toggle="tooltip"]'
            });
            this.fetchRecords();
        }
    }
</script>
