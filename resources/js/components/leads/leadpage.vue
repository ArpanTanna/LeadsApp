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
                <b-alert :show="alertSuccess" variant="success" dismissible>{{alertMessage}}</b-alert>
                <b-alert :show="alertError" variant="danger" dismissible>{{alertMessage}}</b-alert>
            </div>
        </div>

        <!-- Lead Table -->
        <lead-table ref="rLeadTable" :modalUpdatedData="modalUpdatedData" @tableTriggerFrom="tableTriggerFrom" @alertTrigger="alertTrigger"></lead-table>

        <!-- Modal -->
        <lead-modal :modalId="modalId" :modalInitData="modalInitData" @leaddata="modalLeadEmitted" @alertTrigger="alertTrigger"></lead-modal>

    </div>
</template>

<script>
    import leadtable from './leadtable';
    import leadmodal from './leadmodal';

    export default {
        name: 'lead-page',
        components: {
            'lead-table': leadtable,
            'lead-modal': leadmodal,
        },
        data: () => ({
            modalId: '',
            modalInitData: {},
            modalUpdatedData: {},
            alertSuccess: false,
            alertError: false,
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
                this.alertSuccess = false;
                this.alertError = false;
                if(response.type == 1) {
                    this.alertSuccess = true;
                    this.alertMessage = response.message;
                }
                else if(response.type == 2) {
                    this.alertError = true;
                    this.alertMessage = response.message;
                }
            },

            /*
             * From: Table
             * Handle all Emitted actions
             */
            tableTriggerFrom(response) {
                if(response.type === 'edit') {
                    this.editInit(response.data);
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
            }
        },
    }

</script>
