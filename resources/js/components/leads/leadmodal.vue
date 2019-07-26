<template>
<b-modal id="modal-lead-add" ref="modalleadadd" hide-footer no-fade no-close-on-backdrop>
    <template slot="modal-title" v-if="modalId > 0">Edit Lead</template>
    <template slot="modal-title" v-else>Add Lead</template>

    <template slot="default">
        <div class="d-block loader-parent" :class="{'loading': isLoader}">
            <div class="clearfix">
                <div class="col-md-12">
                    <b-form @submit.prevent="validateBeforeSubmit" class="cform" novalidate>
                        <div class="message-container">
                            <b-alert :show="alertSuccess" variant="success" dismissible>{{alertMessage}}</b-alert>
                            <b-alert :show="alertError" variant="danger" dismissible>{{alertMessage}}</b-alert>
                        </div>
                        <b-form-group
                            label="First Name"
                            label-for="first_name"
                        >
                            <b-form-input
                                id="first_name"
                                v-model="modaldata.first_name"
                                v-validate="'required'"
                                type="text"
                                name="first_name"
                                :class="{'input': true, 'is-invalid': errors.has('first_name') }"
                            ></b-form-input>
                        </b-form-group>
                        <b-form-group
                            label="Last Name"
                            label-for="last_name"
                        >
                            <b-form-input
                                id="last_name"
                                v-model="modaldata.last_name"
                                v-validate="'required'"
                                type="text"
                                name="last_name"
                                :class="{'input': true, 'is-invalid': errors.has('last_name') }"
                            ></b-form-input>
                        </b-form-group>
                        <b-form-group
                            label="Email"
                            label-for="email"
                        >
                            <b-form-input
                                id="email"
                                v-model="modaldata.email"
                                v-validate="'required|email'"
                                type="email"
                                name="email"
                                :class="{'input': true, 'is-invalid': errors.has('email') }"
                            ></b-form-input>
                        </b-form-group>
                        <b-form-group
                            label="Phone"
                            label-for="phone"
                        >
                            <b-form-input
                                id="phone"
                                v-model="modaldata.phone"
                                type="number"
                                name="phone"
                            ></b-form-input>
                        </b-form-group>
                        <b-form-group
                            label="Company Name"
                            label-for="company_name"
                        >
                            <b-form-input
                                id="company_name"
                                v-model="modaldata.company_name"
                                v-validate="'required'"
                                type="text"
                                name="company_name"
                                :class="{'input': true, 'is-invalid': errors.has('company_name') }"
                            ></b-form-input>
                        </b-form-group>
                        <b-form-group
                            label="Lead Title"
                            label-for="title"
                        >
                            <b-form-input
                                id="title"
                                v-model="modaldata.other_info.title"
                                v-validate="'required'"
                                type="text"
                                name="title"
                                :class="{'input': true, 'is-invalid': errors.has('title') }"
                            ></b-form-input>
                        </b-form-group>
                        <b-form-group
                            label="City"
                            label-for="city"
                        >
                            <b-form-input
                                id="city"
                                v-model="modaldata.other_info.city"
                                type="text"
                                name="city"
                            ></b-form-input>
                        </b-form-group>
                        <b-form-group>
                            <b-button type="submit" block variant="primary">Submit</b-button>
                        </b-form-group>
                    </b-form>
                </div>
            </div>
        </div>
    </template>
</b-modal>
</template>

<script>
    export default {
        name: 'add-lead',
        props: [
            'modalId', 'modalInitData'
        ],
        data: () => ({
            isLoader: 0,
            modaldata: {'other_info': {}},
            alertSuccess: false,
            alertError: false,
            alertMessage: ''
        }),
        watch: {
            modalId: function(newVal, oldVal) {
                this.$validator.reset();
                this.messageBox({'type': 0});
                this.modaldata = Object.assign({'other_info': {}}, _.cloneDeep(this.modalInitData));
            }
        },
        methods: {
            // Display Error/Success message
            messageBox(response) {
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

            // Validation & Form submit
            validateBeforeSubmit() {
                let $this = this;
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.$emit('alertTrigger', {'type': 0});
                        this.messageBox({'type': 0});
                        $this.isLoader = 1;
                        axios.post('/client/lead/apiaccess/updatelead', this.modaldata)
                        .then((response) => {
                            if(response.data.status == 1) {
                                // Update table
                                $this.$emit('leaddata', {'type': response.data.type, 'data': response.data.lead_data});
                                $this.$refs['modalleadadd'].hide();
                                $this.$emit('alertTrigger', {'type': 1, 'message': response.data.message});
                            }
                            else {
                                this.messageBox({'type': 2, 'message': response.data.message});
                            }
                            $this.isLoader = 0;
                        })
                        .catch((error) => {
                            console.log(error.response);
                            this.messageBox({'type': 2, 'message': 'An error occurred.'});
                            $this.isLoader = 0;
                        });
                    }
                    else {
                        // Error displayed per field
                    }
                });
            }
        },
        mounted() {}
    }
</script>
