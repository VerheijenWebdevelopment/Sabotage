<template>
    <div id="game-role-selection">

        <!-- I. Waiting on our turn -->
        <div id="role-selection__awaiting-turn" v-if="!itsMyTurn && playerRole === null">

            <!-- Title -->
            <h1 id="role-selection__title">Rollen Kiezen</h1>
        
            <!-- Instructions -->
            <div id="role-selection__instructions">Wachten op je beurt.</div>

        </div>

        <!-- II. Select role card -->
        <div id="role-selection__select-role" v-if="itsMyTurn && playerRole === null">

            <!-- Title -->
            <h1 id="role-selection__title">Rollen Kiezen</h1>

            <!-- Instructions -->
            <div id="role-selection__instructions">Selecteer een kaart om jouw rol toegewezen te krijgen.</div>

            <!-- Role cards -->
            <div id="role-cards" v-if="!loading">
                <div class="role-card__wrapper" v-for="(n, ni) in round.num_cards_in_role_deck" :key="ni">
                    <div class="role-card" @click="onClickRoleCard(ni)">
                        <img class="role-card__icon" :src="icons.questions">
                    </div>
                </div>
            </div>

            <!-- Available roles -->
            <div id="available-roles" v-if="!loading">
                <h2 id="available-roles__title">Beschikbare rollen</h2>
                <div id="available-roles__list">
                    <div class="available-role__wrapper" v-for="(roleData, ri) in round.available_roles" :key="ri">
                        <div class="available-role">
                            <div class="available-role__name">{{ getRoleLabelById(roleData.role_id) }}</div>
                            <div class="available-role__amount">x {{ roleData.count }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading -->
            <div id="loading" v-if="loading">
                <div id="loading-icon">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
            </div>

        </div>

        <!-- III. Role selected & waiting on other players -->
        <div id="role-selection__role-selected" v-if="playerRole !== null">

            <!-- Title -->
            <h1 id="role-selection__title">Rol gekozen!</h1>

            <!-- Instructions -->
            <div id="role-selection__instructions">Jij bent uitverkoren om de volgende rol te vervullen</div>

            <!-- My role card -->
            <div id="role-card__wrapper">
                <div id="role-card">
                    <div id="role-name">{{ playerRole.label }}</div>
                    <div id="role-name-subtext" v-if="playerRole.name === 'blue_digger'"><span class="blue-text">Team blauw</span></div>
                    <div id="role-name-subtext" v-if="playerRole.name === 'green_digger'"><span class="green-text">Team groen</span></div>
                </div>
                <div id="role-description">{{ playerRole.description }}</div>
            </div>

            <!-- Waiting for other players -->
            <div id="role-selection__waiting">Zodra iedereen een rol heeft gekozen begint het spel.</div>

        </div>

    </div>
</template>

<script>
    import { EventBus } from './../../event-bus.js';
    export default {
        props: [
            "game",
            "round",
            "roles",
            "player",
            "playerRole",
            "playerAtPlay",
            "icons",
            "apiEndpoints",
        ],
        data: () => ({
            tag: "[game-role-selection]",
            selectedIndex: null,
            loading: false,
        }),
        computed: {
            itsMyTurn() {
                return this.playerAtPlay.id === this.player.id;
            },
        },
        watch: {

        },
        methods: {
            initialize() {
                console.log(this.tag+" initializing");
                console.log(this.tag+" game: ", this.game);
                console.log(this.tag+" round: ", this.round);
                console.log(this.tag+" roles: ", this.roles);
                console.log(this.tag+" player: ", this.player);
                console.log(this.tag+" player role: ", this.playerRole);
                console.log(this.tag+" player at play: ", this.playerAtPlay);
                console.log(this.tag+" icons: ", this.icons);
                console.log(this.tag+" api endpoints: ", this.apiEndpoints);
            },
            getRoleLabelById(id) {
                for (let i = 0; i < this.roles.length; i++) {
                    if (this.roles[i].id === id) {
                        return this.roles[i].label;
                    }
                }
                return "Unknown role";
            },
            onClickRoleCard(index) {
                
                // Flag that we're doing stuff
                this.loading = true;
                
                // Compose API request payload
                let payload = new FormData();
                payload.append("game_id", this.game.id);
                payload.append("action", "selected_role_card");
                payload.append("data", JSON.stringify({ index: index }));

                // Send API request
                this.axios.post(this.apiEndpoints.perform_action, payload)
                    .then(function(response) {
                        console.log(this.tag+" request succeeded, response: ", response.data);
                        this.loading = false;
                        EventBus.$emit("role_selected", {
                            role: response.data.data.role,
                            hand: response.data.data.hand,
                        });
                    }.bind(this))
                    .catch(function(error) {
                        console.warn(this.tag+" request failed, error:", error, error.response.data);
                        this.loading = false;
                    }.bind(this));

            },
        },
        mounted() {
            this.initialize();
        }
    }
</script>

<style lang="scss">
    #game-role-selection {
        width: 100%;
        height: 100%;
        display: flex;
        padding: 100px;
        align-items: center;
        flex-direction: column;
        box-sizing: border-box;
        justify-content: center;
        #role-selection__awaiting-turn, #role-selection__select-role, #role-selection__role-selected {
            width: 100%;
        }
        #role-selection__title {
            text-align: center;
        }
        #role-selection__instructions {
            text-align: center;
            margin: 0 0 75px 0;
        }
        #role-cards {
            display: flex;
            flex-wrap: wrap;
            flex-direction: row;
            justify-content: center;
            margin: 0 -15px -30px -15px;
            .role-card__wrapper {
                box-sizing: border-box;
                padding: 0 15px 30px 15px;
                .role-card {
                    width: 130px;
                    height: 200px;
                    display: flex;
                    border-radius: 3px;
                    transition: all .3s;
                    align-items: center;
                    flex-direction: column;
                    justify-content: center;
                    background-color: hsl(0, 0%, 80%);
                    &:hover {
                        cursor: pointer;
                        background-color: hsl(0, 0%, 95%);
                        .role-card__icon {
                            animation: rotation .2s infinite linear;
                        }
                    }
                    .role-card__icon {
                        width: 50px;
                    }
                }
            }
        }
        #available-roles {
            margin: 50px 0 0 0;
            #available-roles__title {
                font-size: .9em;
                text-align: center;
                text-transform: uppercase;
                color: rgba(255, 255, 255, 0.5);
            }
            #available-roles__list {
                display: flex;
                margin: 0 0 30px 0;
                flex-direction: row;
                justify-content: center;
                .available-role__wrapper {
                    margin: 0 15px 0 0;
                    display: inline-block;
                    &:last-child {
                        margin: 0;
                    }
                    .available-role {
                        flex: 0;
                        display: flex;
                        font-size: .9em;
                        padding: 3px 8px;
                        border-radius: 3px;
                        flex-direction: row;
                        box-sizing: border-box;
                        background-color: hsl(0, 0%, 5%);
                        .available-role__name {

                        }
                        .available-role__amount {
                            margin: 0 0 0 5px;
                        }
                    }
                }
            }
        }
        #loading {
            display: flex;
            flex-direction: row;
            justify-content: center;
            #loading-icon {
                font-size: 3em;
            }
        }
        #role-card__wrapper {
            display: flex;
            align-items: center;
            flex-direction: column;
            justify-content: center;
            #role-card {
                width: 195px;
                height: 300px;
                display: flex;
                border-radius: 3px;
                align-items: center;
                flex-direction: column;
                justify-content: center;
                color: hsl(0, 0%, 15%);
                background-color: hsl(0, 0%, 95%);
                #role-name {
                    font-size: 1.2em;
                    font-weight: 500;
                }
                #role-name-subtext {
                    font-size: .8em;
                    text-transform: uppercase;
                    .green-text {
                        color: #2db200;
                    }
                    .blue-text {
                        color: #0093e2;
                    }
                }
            }
            #role-description {
                width: 500px;
                text-align: center;
                margin: 50px auto 0 auto;
            }
        }
        #role-selection__waiting {
            margin-top: 50px;
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
        }
    }
    @keyframes rotation {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(359deg);
        }
    }
</style>