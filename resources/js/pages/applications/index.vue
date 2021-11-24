<template>
    <v-container fluid>
        <v-row
            justify="center"
        >
            <v-dialog
                v-model="modal"
                persistent
                max-width="600px"
            >
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        class="mb-4"
                        color="primary"
                        dark
                        v-bind="attrs"
                        v-on="on"
                    >
                        Добавить приложение
                    </v-btn>
                </template>
                <v-card>
                    <v-card-title>
                        <span class="text-h5">Добавить приложение</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col
                                    cols="12"
                                    sm="12"
                                    md="12"
                                >
                                    <v-text-field
                                        label="Client ID"
                                        required
                                        v-model="form.client_id"
                                    ></v-text-field>
                                </v-col>
                                <v-col
                                    cols="12"
                                    sm="12"
                                    md="12"
                                >
                                    <v-text-field
                                        label="Client Secret"
                                        required
                                        v-model="form.client_secret"
                                    ></v-text-field>
                                </v-col>
                                <v-col
                                    cols="12"
                                    sm="12"
                                    md="12"
                                >
                                    <v-text-field
                                        label="Redirect URI"
                                        required
                                        v-model="form.redirect_uri"
                                    ></v-text-field>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="blue darken-1"
                            text
                            @click="closeModal"
                        >
                            Закрыть
                        </v-btn>
                        <v-btn
                            @click.prevent="submit"
                            color="blue darken-1"
                            text
                        >
                            Добавить
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-row>

        <v-row dense>
            <v-col
                v-for="application in applications"
                :key="application.id"
                :lg="3"
                :md="4"
                :sm="6"
            >
                <v-card>
                    <v-img
                        class="white--text align-end"
                        gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.1)"
                        height="200px"
                    >
                        <div
                            class="d-flex justify-content-center text-h2 white--text"
                            style="height: 100%;"
                        >
                            <v-btn
                                color="primary"
                                dark
                                :href="'get-code/'+application.id"
                            >
                                +
                            </v-btn>
                        </div>
                        <v-card-title>{{application.count}} постов</v-card-title>
                        <v-card-text>{{application.vk_token_expires}}</v-card-text>
                    </v-img>

                    <v-card-actions>

                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>

    </v-container>
</template>
<script>
export default {
    data: () => ({
        select: ['ky'],
        applications: {},
        modal: false,
        form: new Form({
            client_id: '',
            client_secret: '',
            redirect_uri: ''
        })
    }),
    methods: {
        submit() {
            this.form.post('/api/application')
                .then(({data}) => {
                    this.closeModal();
                    console.log(data)
                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    });
                    this.loadApplications()
                })
                .catch(() => {
                    console.log(this.form);
                });
        },
        closeModal() {
            this.modal = false;
            this.form.reset();
        },
        loadApplications() {
            axios.get("api/application")
                .then(({data}) => {
                    this.applications = data
                });
        }
    },
    created() {
        this.loadApplications()
    }
}
</script>
