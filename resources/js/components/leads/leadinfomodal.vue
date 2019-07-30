<template>
    <b-modal id="modal-lead-info" ref="modalleadinfo" hide-footer no-fade no-close-on-backdrop>
        <template slot="modal-title">Email send Information</template>

        <template slot="default">
            <div class="d-block loader-parent" :class="{'loading': isLoader}">
                <div class="clearfix">
                    <div class="message-container">
                        <b-alert :show="alertType === 'success'" variant="success" dismissible>{{alertMessage}}</b-alert>
                        <b-alert :show="alertType === 'error'" variant="danger" dismissible>{{alertMessage}}</b-alert>
                    </div>
                    <div class="clearfix ei-box rounded" v-for="item in modaldata">
                        <h5 class="m-0">
                            Funnel: {{item.name}}
                            &nbsp;<a href="javascript:void(0);" class="small" title="Sent Successfully" v-if="item.is_sent"><i class="fa fa-paper-plane"></i> </a>
                            <a href="javascript:void(0);" class="small" title="Viewed by User" v-if="item.is_open"><i class="fa fa-eye"></i> </a>
                        </h5>
                        <p class="m-0 small">
                            <span>{{ item.created_at | formatDate}}</span>
                        </p>
                    </div>
                </div>
            </div>
        </template>
    </b-modal>
</template>

<script>

    import moment from 'moment'

    Vue.filter('formatDate', function(value) {
        if (value) {
            return moment(String(value)).format('Do MMM YYYY')
        }
    });

    export default {
        name: 'lead-info-modal',
        props: [
            'modalId'
        ],
        data: () => ({
            isLoader: 0,
            modaldata: [1],
            alertType: '',
            alertMessage: ''
        }),
        watch: {
            modalId: function(newVal, oldVal) {
                if(newVal !== '') {
                    this.loadData(newVal);
                }
            }
        },
        methods: {
            // Display Error/Success message
            messageBox(response) {
                this.alertType = '';
                this.alertMessage = '';
                if(response.type == 1) {
                    this.alertType = 1;
                    this.alertMessage = response.message;
                }
                else if(response.type == 2) {
                    this.alertType = 2;
                    this.alertMessage = response.message;
                }
            },

            // Fetch emailinfo data
            loadData(leadId) {
                this.messageBox({'type': 0});
                this.isLoader = 1;
                axios.post('/client/lead/apiaccess/leadinfo', {'id': leadId})
                    .then((response) => {
                        if(response.data.status == 1) {
                            this.modaldata = response.data.records;
                            this.messageBox({'type': '', 'message': ''});
                        }
                        else {
                            this.messageBox({'type': 2, 'message': response.data.message});
                        }
                        this.isLoader = 0;
                    })
                    .catch((error) => {
                        console.log(error.response);
                        this.messageBox({'type': 2, 'message': 'An error occurred.'});
                        this.isLoader = 0;
                    });
            }
        },
        mounted() {}
    }
</script>
