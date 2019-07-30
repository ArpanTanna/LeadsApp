<template>
    <div class="clearfix">
        <div class="pagetitle-wrap">
            <h1>Leads</h1>
            <div class="pt-portion ml-auto">
                <b-button @click="addInit" variant="primary">Add Lead</b-button>
            </div>
        </div>
        <div class="message-box-container">
            <div class="message-container">
                <b-alert :show="alertType === 'success'" variant="success" dismissible>{{alertMessage}}</b-alert>
                <b-alert :show="alertType === 'error'" variant="danger" dismissible>{{alertMessage}}</b-alert>
            </div>
        </div>

        <!-- Lead Table -->
        <lead-table ref="rLeadTable" :modalUpdatedData="modalUpdatedData" @tableTriggerFrom="tableTriggerFrom" @alertTrigger="alertTrigger"></lead-table>

        <!-- Lead Edit Modal -->
        <lead-modal :modalId="modalId" :modalInitData="modalInitData" @leaddata="modalLeadEmitted" @alertTrigger="alertTrigger"></lead-modal>

        <!-- Lead Info Modal -->
        <lead-info-modal :modalId="modalId"></lead-info-modal>

    </div>
</template>

<script>
    import leadtable from './leadtable';
    import leadmodal from './leadmodal';
    import leadinfomodal from './leadinfomodal';

    export default {
        name: 'lead-page',
        components: {
            'lead-table': leadtable,
            'lead-modal': leadmodal,
            'lead-info-modal': leadinfomodal,
        },
        data: () => ({
            modalId: '',
            modalInitData: {},
            modalUpdatedData: {},
            alertType: '',
            alertMessage: ''
         }),
        methods: {
            /*
             * From: Modal
             * Updated Lead data
             */
            modalLeadEmitted(value) {
                this.modalUpdatedData = Object.assign({}, value);
            },

            /*
             * From: Modal & Table
             * Set Alert
             */
            alertTrigger(response) {
                this.alertType = '';
                let $this = this;
                setTimeout(function() {
                    if(response.type == 1) {
                        $this.alertType = 'success';
                        $this.alertMessage = response.message;
                    }
                    else if(response.type == 2) {
                        $this.alertType = 'error';
                        $this.alertMessage = response.message;
                    }
                }, 200);
            },

            /*
             * From: Table
             * Handle all Emitted actions
             */
            tableTriggerFrom(response) {
                if(response.type === 'edit') {
                    this.editInit(response.data);
                }
                else if(response.type === 'infoModal') {
                    this.infoModal(response.data);
                }
            },

            addInit() {
                this.modalId = '';
                this.modalInitData = {'other_info': {}};
                this.$bvModal.show('modal-lead-add');
            },

            editInit(data) {
                this.modalId = data.id;
                this.modalInitData = Object.assign({}, data);
                this.$bvModal.show('modal-lead-add');
            },

            infoModal(data) {
                this.modalId = data.id;
                this.$bvModal.show('modal-lead-info');
            }
        },
    }

</script>
