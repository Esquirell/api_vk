<template>
    <v-container fluid>

        <v-row justify="center">
            <v-dialog
                v-model="modal"
                persistent
                max-width="600px"
            >
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
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
                                        :error-messages="form.errors.errors.url"
                                        label="Введите ссылку на группу"
                                        required
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
                                        :items="items"
                                        item-text="id"
                                        item-value="access_token"
                                        label="Приложение"
                                        required
                                        v-model="form.access_token"
                                    >
                                        <template v-slot:selection="data">
                                            <!-- HTML that describe how select should render selected items -->
                                            {{ data.item.id }}) {{ data.item.count }} запросов
                                        </template>
                                        <template v-slot:item="data">
                                            <!-- HTML that describe how select should render items when the select is open -->
                                            {{ data.item.id }}) {{ data.item.count }} запросов
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


        <v-text-field v-model="form.title" label="Another input"></v-text-field>
        <v-btn
            v-on:click="dobav"
            depressed>
            Normal
        </v-btn>

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
                    </v-img>

                    <v-card-actions>
                        <router-link
                            v-if="knowledge === 100"
                            :to="'/api/group' + group.id"
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
                                Extra large Button
                            </v-btn>
                        </router-link>

                        <v-progress-linear
                            v-else
                            v-model="knowledge"
                            height="25"
                        >
                            <strong>{{ Math.ceil(knowledge) }}%</strong>
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
        select: ['ky'],
        items: {},
        modal: false,
        groups: {},
        knowledge: 100,
        form: new Form({
            url: '',
            count_posts: '',
            access_token: '',
        })
    }),
    methods: {
        submit() {
            this.form.post('/api/group')
                .then(({data}) => {
                    this.closeModal();
                    console.log(data)
                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    });
                })
                .catch(() => {
                    console.log(this.form);
                });
        },
        closeModal() {
            this.modal = false;
            this.form.reset();
        }
    },
    created() {
        axios.get("api/group")
            .then(({data}) => {
                this.groups = data
            });

        axios.get("api/application")
            .then(({data}) => {
                this.items = data
            });
    }
}
</script>
