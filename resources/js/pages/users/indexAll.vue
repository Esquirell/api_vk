<style>
.v-card--reveal {
    align-items: center;
    bottom: 0;
    justify-content: center;
    opacity: 0.8;
    position: absolute;
    width: 100%;
}
</style>
<template>
    <v-container fluid>
        <v-pagination
            v-model="page"
            :length="length"
            circle
            @input="getUserFromGroup(page)"
        >

        </v-pagination>
        <v-row>
            <v-col
                class="mt-2"
                v-for="user in users"
                :key="user.id"
                :lg="4"
                :md="6"
                :sm="12"
                :xs="12"
            >

                <v-card class="">
                    <v-hover v-slot="{ hover }">
                        <v-img
                            contain
                            :src="user.photo"
                            class="white--text align-end"
                            height="300px"
                        >
                            <v-card-text class="bg-dark p-0 pl-5">{{ user.first_name }} {{ user.last_name }} | {{ user.bdate }}</v-card-text>
                            <v-expand-transition>
                                <div
                                    v-if="hover"
                                    class="d-flex transition-fast-in-fast-out teal darken-2 v-card--reveal white--text"
                                    style="height: 100%;"
                                >
                                    <div>
                                        <p v-for="note in user.notes">{{ note.title }}</p>
                                        <p v-for="group in user.groups">{{ group.title }}</p>
                                    </div>
                                </div>
                            </v-expand-transition>
                        </v-img>
                    </v-hover>
                    <v-card-actions
                    >
                        <v-btn
                            target="_blank"
                            :href="user.url"
                            x-large
                            icon
                        >
                            <v-icon
                                color="cyan accent-1"
                            >mdi-android-messages
                            </v-icon>
                        </v-btn>

                        <v-card-text class="p-0 pr-1">{{ user.last_seen }}</v-card-text>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
        <v-pagination
            v-model="page"
            :length="length"
            circle
            @input="getUserFromGroup(page)"
        >

        </v-pagination>
    </v-container>
</template>

<script>
export default {
    data: () => ({
        users: {},
        length: 0,
        page: 1,
        groupId: '',
    }),
    methods: {
        getUserFromGroup(page = 1) {
            axios.get('/api/user/?page=' + page)
                .then(({data}) => {
                    this.users = data.data
                    this.length = data.last_page
                    console.log(data)
                })
                .catch(() => {
                });
        }
    },
    mounted() {
        console.log(this.$route.params.id);
        this.getUserFromGroup();
    }
}
</script>
