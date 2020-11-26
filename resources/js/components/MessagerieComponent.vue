<template>
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <template v-for="conversation in conversations">
                    <div class="list-group">
                        <router-link :to="{name: 'conversation', params: {id: conversation.id} }" class="list-group-item d-flex justify-content-between align-items-center">
                            {{ conversation.name }}
                            <span class="badge badge-pill badge-primary" v-if="conversation.unread">{{ conversation.unread }}</span>
                        </router-link>
                    </div>
                </template>
            </div>
            <div class="col-md-9">
                <router-view></router-view>
            </div>
        </div>
    </div>
</template>

<script>

    import {mapGetters} from 'vuex'

    export default {


        props: {
            user: Number
        },

        computed: {
          ...mapGetters(['conversations'])
        },

        mounted() {

            this.$store.dispatch('loadConversations')
            this.$store.dispatch('setUser', this.user)

        }


    }
</script>
