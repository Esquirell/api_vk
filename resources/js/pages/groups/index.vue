<template>
    <v-container fluid>
        <v-row justify="center">
            <v-btn
                class="mb-4"
                color="primary"
                dark
                @click="updateOnline"
            >
                Обновить
            </v-btn>
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
                        Добавить группу
                    </v-btn>
                </template>
                <v-card>
                    <v-card-title>
                        <span class="text-h5">Добавить группу</span>
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
                                        @change="checkAviableGroup"
                                        :error-messages="error"
                                        label="Введите ссылку на группу"
                                        required
                                        :success-messages="success_messages"
                                        v-model="form.url"
                                    ></v-text-field>
                                </v-col>
                                <v-col
                                    cols="12"
                                    sm="12"
                                    md="12"
                                >
                                    <v-text-field
                                        :error-messages="form.errors.errors.count_posts"
                                        label="Количество постов"
                                        required
                                        v-model="form.count_posts"
                                    ></v-text-field>
                                </v-col>
                                <v-col
                                    cols="12"
                                    sm="12"
                                >
                                    <v-select
                                        :items="applications"
                                        :error-messages="form.errors.errors.access_token"
                                        item-text="id"
                                        item-value="access_token"
                                        label="Приложение"
                                        required
                                        v-model="form.access_token"
                                    >
                                        <template v-slot:selection="data">
                                            <!-- HTML that describe how select should render selected items -->
                                            {{ data.item.id }}) {{ data.item.count }} запросов.
                                        </template>
                                        <template v-slot:item="data">
                                            <!-- HTML that describe how select should render items when the select is open -->
                                            {{ data.item.id }}) {{ data.item.count }} запросов.
                                        </template>
                                    </v-select>
                                </v-col>
                                <v-col
                                    cols="12"
                                    sm="6"
                                >
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
                v-for="group in groups"
                :key="group.id"
                :lg="3"
                :md="4"
                :sm="6"
            >
                <v-card>
                    <v-img
                        :src="group.image"
                        class="white--text align-end"
                        gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                        height="200px"
                    >
                        <v-card-title v-text="group.title"></v-card-title>
                        <v-card-text>
                            <p>
                                {{group.status}}
                            </p>
                        </v-card-text>
                    </v-img>

                    <v-card-actions>
                        <router-link
                            v-if="group.progress === 100"
                            :to="'/group/' + group.id"
                            exact-path
                            custom
                            v-slot="{ href, route, navigate, isActive, isExactActive }"
                        >
                            <v-btn
                                :href="href"
                                @click="navigate"

                                block
                                x-large
                                color="primary"
                                dark
                            >
                                Открыть
                            </v-btn>
                        </router-link>

                        <v-progress-linear
                            v-else
                            v-model="group.progress"
                            height="25"
                        >
                            <strong>{{ Math.ceil(group.progress) }}%</strong>
                        </v-progress-linear>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>

    </v-container>
</template>
<script>
export default {
    data: () => ({
        error: [],
        success_messages: [],
        select: ['ky'],
        items: {},
        applications: {},
        modal: false,
        groups: [],
        knowledge: 100,
        form: new Form({
            url: '',
            count_posts: '',
            access_token: '',
        })
    }),
    methods: {
        dobav() {
            console.log(this.form.title);
            this.knowledge += 30;
        },
        submit() {
            this.form.post('/api/group')
                .then(({data}) => {
                    setTimeout(this.loadGroups(), 2000);
                    this.closeModal();
                    // console.log(data)
                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    });
                })
                .catch(() => {
                    // console.log(this.form);
                });
        },
        closeModal() {
            this.modal = false;
            this.form.reset();
        },

        updateOnline() {
            axios.post("api/users/update-online")
                .then(({data}) => {
                    console.log('pf')
                })
        },
        checkAviableGroup() {

            axios.post("api/check_group", {
                url: this.form.url
            })
                .then(({data}) => {
                    this.error = []
                    this.success_messages = data
                })
            .catch((error) => {
                this.success_messages = []
                this.error = error.response.data
            })
        },
        loadGroups() {
            axios.get("api/group")
                .then(({data}) => {
                    console.log(data)
                    this.groups = data
                    let searchTerm = 3;
                    let groupId = this.groups.find(group => group.id === searchTerm)
                });
        }
    },
    created() {
        this.loadGroups()

        axios.get("api/application/free")
            .then(({data}) => {
                this.applications = data
            });
    },
    mounted() {
        Echo.channel('progress')
            .listen('ProgressAddedEvent', (e) => {
                console.log(e)
                let group_id = e.group_id;
                let progress = e.progress;
                let status = e.status;
                let group = this.groups.find(group => group.id === group_id)
                let index = this.groups.indexOf(group)
                this.groups[index].progress = progress
                this.groups[index].status = status
            })
        Echo.channel('group-added')
            .listen('GroupAddedEvent', (e) => {
                this.groups.push(e.group)
            })
    },

}
</script>
