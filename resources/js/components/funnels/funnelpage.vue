<template>
    <div class="clearfix">
        <div class="pagetitle-wrap">
            <h1>Funnels</h1>
            <div class="pt-portion ml-auto">
                <b-button @click="addInit" variant="primary">Add Funnel</b-button>
            </div>
        </div>
        <div class="clearfix mt-4">
            <div class="message-box-container">
                <div class="message-container">
                    <b-alert :show="alertType === 'success'" variant="success" dismissible>{{alertMessage}}</b-alert>
                    <b-alert :show="alertType === 'error'" variant="danger" dismissible>{{alertMessage}}</b-alert>
                </div>
            </div>
            <div class="col-lg-8 loader-parent" :class="{'loading': isLoader}">
                <b-form @submit.prevent="validateBeforeSubmit('form-main')" class="cform" novalidate data-vv-scope="form-main">
                    <b-form-group
                        label="Select Funnel"
                        label-for="ukey"
                    >
                        <b-input-group>
                            <b-form-select
                                id="ukey"
                                v-model="funnelData.ukey"
                                :options="funnelList"
                                name="ukey"
                                @change="editInit($event)"
                            ></b-form-select>
                            <div class="input-group-addon" v-if="funnelData.ukey !== ''">
                                <a href="javascript:void(0);" class="btn btn-danger" @click="deleteInit"><i class="fa fa-trash"></i> </a>
                            </div>
                        </b-input-group>
                    </b-form-group>
                    <div class="clearfix">
                        <b-form-group
                            label="Name"
                            label-for="name"
                        >
                            <b-form-input
                                id="subject"
                                v-model="funnelData.name"
                                v-validate="'required'"
                                type="text"
                                name="name"
                                :class="{'input': true, 'is-invalid': errors.has('form-main.name') }"
                            ></b-form-input>
                        </b-form-group>
                        <b-form-group
                            label="Subject"
                            label-for="subject"
                        >
                            <b-form-input
                                id="subject"
                                v-model="funnelData.subject"
                                v-validate="'required'"
                                type="text"
                                name="subject"
                                :class="{'input': true, 'is-invalid': errors.has('form-main.subject') }"
                            ></b-form-input>
                        </b-form-group>
                        <b-form-group
                            label="Content"
                            label-for="content"
                            :class="{'is-invalid-box': errors.has('form-main.content') }"
                        >
                            <ckeditor :editor="editor" name="content" id="content" v-model="funnelData.content"
                                      v-validate="'required'"
                                      @ready="onReady"
                            ></ckeditor>
                        </b-form-group>
                        <b-form-group>
                            <b-button type="submit" block variant="primary">Submit</b-button>
                        </b-form-group>
                    </div>
                </b-form>
            </div>
        </div>
    </div>
</template>

<script>
    import CKEditor from '@ckeditor/ckeditor5-vue';
    //import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
    import DecoupledEditor from '@ckeditor/ckeditor5-build-decoupled-document';


    export default {
        name: 'funnel-page',
        components: {
            ckeditor: CKEditor.component
        },
        data: () => ({
            alertType: '',
            alertMessage: '',
            isLoader: 0,
            config: {'name':'content'},
            editor: DecoupledEditor,
            funnelList: [{'value': '', 'text': 'Add New Funnel'}],
            funnelData: {'ukey': ''},
         }),
        methods: {
            onReady( editor )  {
                // Insert the toolbar before the editable area.
                editor.ui.getEditableElement().parentElement.insertBefore(
                    editor.ui.view.toolbar.element,
                    editor.ui.getEditableElement()
                );
            },

            // Display Error/Success message
            messageBox(response) {
                this.alertType = response.type;
                this.alertMessage = response.message;
            },

            addInit() {
                this.funnelData = {'ukey': ''};
            },

            editInit(event) {
                if(event !== '') {
                    let $this = this;
                    this.messageBox({'type': '', 'message': ''});
                    $this.isLoader = 1;
                    axios.post('/client/funnel/apiaccess/fetchdata', this.funnelData)
                        .then((response) => {
                            if (response.data.status == 1) {
                                this.funnelData = response.data.data;
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
                    this.addInit();
                }
            },

            deleteInit() {
                if(confirm("Do you really want to delete this funnel?"))
                {
                    let $this = this;
                    $this.isLoader = 1;

                    axios.delete('/client/funnel/'+$this.funnelData.ukey, {})
                        .then((response) => {
                            if(response.data.status == 1) {
                                // Delete row
                                let itemIndex = $this.funnelList.findIndex(f => f.value === $this.funnelData.ukey);
                                $this.funnelList.splice(itemIndex, 1);
                                $this.addInit();
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

            validateBeforeSubmit(scope) {
                let $this = this;
                this.$validator.validateAll(scope).then((result) => {
                    if (result) {
                        this.messageBox({'type': '', 'message': ''});
                        $this.isLoader = 1;
                        axios.post('/client/funnel/apiaccess/updatefunnel', this.funnelData)
                            .then((response) => {
                                if(response.data.status == 1) {
                                    if(response.data.type === 'update') {
                                        Vue.set($this.funnelList, $this.funnelData.id, response.data.data);
                                    }
                                    else {
                                        $this.funnelList.unshift(response.data.data);
                                        Vue.set($this.funnelData, 'id', response.data.data.value);
                                    }
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
        }
    }

</script>
