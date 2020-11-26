<template>
    <div>
        <div class="card">
            <div class="card-header">{{ name }}</div>
            <div class="card-body messagerie__body">
                <Message :message="message" v-for="message in messages" :user="user" :key="message.id"></Message>

                <form>
                    <div class="form-group">
                        <textarea name="content" v-model="content" id="" :class="{'form-control': true, 'is-invalid': errors['content']} " placeholder="Type your message..." @keypress.enter="sendMessage"></textarea>
                        <div class="invalid-feedback">{{ error }}</div>
                    </div>
                </form>

                <div class="messagerie_loading" v-if="loading">
                    <div class="loader"></div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    import Message from "./MessageComponent";
    import {mapGetters} from 'vuex'
    export default {

        components: {
          Message
        },

        data () {
          return {
              content: '',
              errors: [],
              error: '',
              loading: false
          }
        },

        computed: {

            ...mapGetters(['user']),

            messages: function () {
                return this.$store.getters.messages(this.$route.params.id)
            },

            lastMessage: function () {

                return this.messages[this.messages.length - 1]

            },

            name: function () {
                return this.$store.getters.conversation(this.$route.params.id).name
            },

            count: function () {
                return this.$store.getters.conversation(this.$route.params.id).count
            },

        },

        mounted() {

            this.loadMessages()
            this.$message = this.$el.querySelector('.messagerie__body')
            document.addEventListener('visibilitychange', this.onVisible)

        },

        destroyed() {

            document.removeEventListener('visibilitychange', this.onVisible)

        },

        watch: {

            '$route.params.id': function () {
                this.loadMessages()
            },

            lastMessage: function () {
                this.scrollBot()
            }

        },

        methods: {

            onVisible () {

                if (document.hidden === false) {

                    this.$store.dispatch('loadMessages', this.$route.params.id)

                }

            },

            async loadMessages () {
                let response = await this.$store.dispatch('loadMessages', this.$route.params.id)

                if (this.messages.length < this.count) {
                    this.$message.addEventListener('scroll', this.onScroll)
                }
            },

            async onScroll () {

               if (this.$message.scrollTop === 0) {

                    this.loading = true
                    this.$message.removeEventListener('scroll', this.onScroll)

                   let previousHeight = this.$message.scrollHeight

                   await this.$store.dispatch('loadPreviousMessages', this.$route.params.id)

                   this.$nextTick(() => {
                       this.$message.scrollTop = this.$message.scrollHeight - previousHeight
                   })

                   if (this.messages.length < this.count) {
                       this.$message.addEventListener('scroll', this.onScroll)
                   }

                   this.loading = false
                }


            },

            scrollBot () {

                this.$nextTick(() => {
                    this.$message.scrollTop = this.$message.scrollHeight
                })
            },

            async sendMessage (e) {

                if (e.shiftKey === false) {
                    this.loading = true
                    this.errors = {}
                    this.error = ''
                    e.preventDefault()
                    try {
                        await this.$store.dispatch('sendMessage', {
                            content: this.content,
                            userId: this.$route.params.id
                        })
                        this.content = ''
                    } catch (e) {
                        if(e.errors) {
                            this.errors = e.errors
                            this.error = e.errors.content.join(',')
                        }else {
                            console.log(e)
                        }
                    }
                    this.loading = false
                }

            },


        }

    }
</script>
