<template>
    <div id="leaderboards">

        <!-- Users -->
        <div id="users" class="elevation-1" v-if="mutableUsers.length > 0">
            <div id="users-headers">
                <div class="users-header">Speler</div>
                <div class="users-header">Aantal gespeelde spellen</div>
                <div class="users-header">Goud gevonden</div>
            </div>
            <div class="user" v-for="(user, ui) in mutableUsers" :key="ui">
                <div class="username">{{ user.username }}</div>
                <div class="games_played">{{ user.games_played }}</div>
                <div class="highscore">{{ user.highscore }}</div>
            </div>
        </div>

        <!-- No users -->
        <div id="no-users" class="elevation-1" v-if="mutableUsers.length === 0">
            No users could be found, weiiiiird :(
        </div>

    </div>
</template>

<script>
    export default {
        props: [
            "users",
        ],
        data: () => ({
            tag: "[leaderboards]",
            mutableUsers: [],
        }),
        methods: {
            initialize() {
                console.log(this.tag+" initializing");
                console.log(this.tag+" users: ", this.users);
                this.initializeData();
            },
            initializeData() {
                if (this.users !== undefined && this.users !== null && this.users.length > 0) {
                    for (let i = 0; i < this.users.length; i++) {
                        this.mutableUsers.push(this.users[i]);
                    }
                }
            }
        },
        mounted() {
            this.initialize();
        }
    }
</script>

<style lang="scss">
    #leaderboards {
        #users {
            overflow: hidden;
            border-radius: 3px;
            background-color: #fff;
            #users-headers {
                display: flex;
                color: #ffffff;
                padding: 15px 20px;
                flex-direction: row;
                box-sizing: border-box;
                background-color: hsl(0, 0%, 15%);
                border-bottom: 1px solid rgba(0, 0, 0, 0.1);
                .users-header {
                    flex: 1;
                    display: flex;
                    font-weight: 500;
                    flex-direction: row;
                    align-items: center;
                    &:nth-child(2) {
                        justify-content: center;
                    }
                    &:last-child {
                        justify-content: flex-end;
                    }
                }
            }
            .user {
                display: flex;
                padding: 15px 20px;
                flex-direction: row;
                box-sizing: border-box;
                border-bottom: 1px solid rgba(0, 0, 0, 0.1);
                .username {
                    flex: 1;
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                }
                .games-played {
                    flex: 1;
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                    justify-content: center;
                }
                .highscore {
                    flex: 1;
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                    justify-content: flex-end;
                }
            }
        }
        #no-users {
            padding: 25px;
            overflow: hidden;
            border-radius: 3px;
            text-align: center;
            box-sizing: border-box;
            background-color: #fff;
        }
    }
</style>