<template>
    <v-container fluid>
        <v-row>
            <v-text-field
                label="Введите запрос"
                required
                v-model="search_string"
            ></v-text-field>
            <v-btn
                @click.prevent="submit"
                color="blue darken-1"
            >
                Поиск
            </v-btn>
        </v-row>
        <v-row class="justify-content-center">
            <v-progress-circular
                v-if="load"
                :size="70"
                :width="7"
                color="purple"
                indeterminate
            ></v-progress-circular>
        </v-row>
        <v-simple-table
            v-if="!load && users.length !== 0"
        >
            <thead>
            <tr>
                <th class="text-left">
                    ID
                </th>
                <th class="text-left">
                    Фото
                </th>
                <th class="text-left">
                    Имя
                </th>
                <th class="text-left">
                    Фамилия
                </th>
                <th class="text-left">
                    Группы
                </th>
                <th class="text-left">
                    Посты
                </th>
            </tr>
            </thead>
            <tbody>
            <tr
                v-for="user in users"
                :key="user.id"
            >
                <td>{{ user.id }}</td>
                <td>
                    <v-avatar
                        class="mt-3 mb-3"
                        size="128">
                        <img
                            :src=user.photo
                        >
                    </v-avatar>
                </td>
                <td>{{ user.first_name }}</td>
                <td>{{ user.last_name }}</td>
                <td>
                    <p
                        v-for="group in user.groups">
                        <a
                            :href=group.url_group
                        >{{group.title}}</a>
                    </p>
                </td>
                <td>
                    <p
                        v-for="post in user.posts">
                        <a
                            :href=post.url
                        >Пост</a>
                    </p>
                    <p
                        v-if="user.posts.length === 0"
                    >Постов нет</p>
                </td>
            </tr>
            </tbody>
        </v-simple-table>
    </v-container>
</template>

<script>
export default {
    data() {
        return {
            load: 0,
            search_string: '',
            users: [],
        }
    },
    methods: {
        submit() {
            this.load = 1
            axios.post("api/user/search", {
                search_string: this.search_string,
            })
                .then(({data}) => {
                    this.load = 0
                    console.log(data)
                    this.users = data
                })
                .catch((error) => {
                    console.log(error)
                })
        },
    },
    mounted() {
    }
}
</script>
