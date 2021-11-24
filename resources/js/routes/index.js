import GroupIndex from '../pages/groups/index.vue'
import UsersIndex from '../pages/users/index.vue'
import UsersAll from '../pages/users/indexAll.vue'
import SearchUser from '../pages/users/search'
import ApplicationIndex from '../pages/applications/index'

export default {
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'groupindex',
            component: GroupIndex,
        },
        {
            path: '/group/:id',
            name: 'usersindex',
            component: UsersIndex,
        },
        {
            path: '/users',
            name: 'userall',
            component: UsersAll,
        },
        {
            path: '/applications',
            name: 'applicationindex',
            component: ApplicationIndex,
        },
        {
            path: '/search',
            name: 'searchuser',
            component: SearchUserl,
        },
    ]
}
