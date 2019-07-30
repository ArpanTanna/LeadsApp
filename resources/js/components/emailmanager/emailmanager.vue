<template>
    <div class="clearfix">
        <div class="pagetitle-wrap">
            <h1>Email Manager</h1>
            <div class="pt-portion ml-auto">
                <b-button variant="primary">Send</b-button>
            </div>
        </div>
        <div class="clearfix mt-4 loader-parent" :class="{'loading': isLoader}">
            <div class="message-box-container">
                <div class="message-container">
                    <b-alert :show="alertType === 'success'" variant="success" dismissible>{{alertMessage}}</b-alert>
                    <b-alert :show="alertType === 'error'" variant="danger" dismissible>{{alertMessage}}</b-alert>
                </div>
            </div>
            <div class="row clearfix">
                <div class="col-xl-8 col-12 order-1 order-xl-0">
                    <b-card>
                        <template slot="header">Select Leads</template>
                        <b-table class="ttype1" ref="tableEM" striped hover responsive
                                 selectable select-mode="multi" @row-selected="rowSelected"
                                 :items="items" :fields="fields">
                            <template slot="name" slot-scope="data">
                                {{ data.item.first_name }} {{ data.item.last_name }}
                            </template>
                            <template slot="company_name" slot-scope="data">
                                {{ data.item.company_name }}
                            </template>
                        </b-table>
                    </b-card>
                </div>
                <div class="col-xl-4 col-12 order-0 order-xl-1 mb-3">
                    <b-card>
                        <template slot="header">Select Funnel</template>
                        <b-form @submit.prevent="validateBeforeSubmit('form-main')" class="cform" novalidate data-vv-scope="form-main">
                            <b-form-group
                                label="Select Funnel"
                                label-for="ukey"
                            >
                                <b-form-select
                                    id="ukey"
                                    v-model="funnel"
                                    :options="funnelList"
                                    name="funnel"
                                    v-validate="'required'"
                                    :class="{'input': true, 'is-invalid': errors.has('form-main.name') }"
                                ></b-form-select>
                            </b-form-group>
                            <b-form-group v-if="leadSelected.length == 0">
                                <div class="invalid-feedback d-block">
                                    Please select at least one lead for sending email.
                                </div>
                            </b-form-group>
                            <b-form-group>
                                <b-button type="submit" block variant="primary">Send Emails</b-button>
                            </b-form-group>
                        </b-form>
                    </b-card>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        name: 'emailmanager-page',
        components: {

        },
        data: () => ({
            isLoader: 0,
            alertType: '',
            alertMessage: '',
            fields: [
                {'key': 'name', 'sortable': false},
                {'key': 'email', 'sortable': false},
                {'key': 'company_name', 'sortable': false}
            ],
            items: [],
            leadSelected: [],
            funnelList: [{'value': '', 'text': 'Select Funnel'}],
            funnel: ''
         }),
        methods: {
            // Display Error/Success message
            messageBox(response) {
                this.alertType = response.type;
                this.alertMessage = response.message;
            },

            //Get selected lead ids
            rowSelected(items) {
                let selectedIds = [];
                items.map(function (item) {
                    selectedIds.push(item.id);
                });
                this.leadSelected = selectedIds;
            },

            validateBeforeSubmit(scope) {
                let $this = this;
                this.$validator.validateAll(scope).then((result) => {
                    if (result &&  $this.leadSelected.length > 0) {
                        this.messageBox({'type': '', 'message': ''});
                        $this.isLoader = 1;
                        axios.post('/client/emailmanager/apiaccess/sendemail', {'funnel': this.funnel, 'leads': this.leadSelected})
                            .then((response) => {
                                if(response.data.status == 1) {
                                    this.messageBox({'type': 'success', 'message': response.data.message});
                                }
                                else {
                                    this.messageBox({'type': 'error', 'message': response.data.message});
                                }
                                $this.isLoader = 0;
                            })
                            .catch((error) => {
                                console.log(error.response);
                                this.messageBox({'type': 'error', 'message': 'An error occurred.'});
                                $this.isLoader = 0;
                            });
                    }
                    else {
                        // Error displayed per field
                    }
                });
            }
        },
        mounted() {
            this.funnelList.push.apply(this.funnelList, JSON.parse(funnels));
            this.items.push.apply(this.items, JSON.parse(leads));
        }
    }

</script>
