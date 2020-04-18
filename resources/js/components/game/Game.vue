<template>
    <div id="game__wrapper">

        <!-- Game -->
        <div id="game">
            <div id="game-content">

            </div>
            <div id="game-sidebar">
                <div id="game-sidebar__players">
                    <game-players
                        :icons="icons"
                        :game="mutableGame"
                        :player="mutablePlayer"
                        :player-at-play="playerAtPlay"
                        v-model="mutablePlayers">
                    </game-players>
                </div>
                <div id="game-sidebar__chat">
                    <game-chat
                        :game="game"
                        :player="player"
                        :send-message-api-endpoint="apiEndpoints.send_message">
                    </game-chat>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        props: [
            "game",
            "round",
            "player",
            "playerRole",
            "hand",
            "roles",
            "cards",
            "icons",
            "apiEndpoints",
        ],
        data: () => ({
            tag: "[game]",
            mutableGame: [],
            mutableRound: [],
            mutablePlayer: null,
            mutablePlayerRole: null,
            mutablePlayers: [],
            mutableHand: [],
        }),
        computed: {
            playerAtPlay() {
                for (let i = 0; i < this.mutablePlayers.length; i++) {
                    if (this.mutablePlayers[i].player_number == this.mutableRound.players_turn) {
                        return this.mutablePlayers[i];
                    }
                }
                return false;
            },
        },
        methods: {
            initialize() {
                // console.log(this.tag+" initializing");
                // console.log(this.tag+" game: ", this.game);
                // console.log(this.tag+" round: ", this.round);
                // console.log(this.tag+" player: ", this.player);
                // console.log(this.tag+" player role: ", this.playerRole);
                // console.log(this.tag+" hand: ", this.hand);
                // console.log(this.tag+" roles: ", this.roles);
                // console.log(this.tag+" cards: ", this.cards);
                // console.log(this.tag+" icons: ", this.icons);
                // console.log(this.tag+" api endpoints: ", this.apiEndpoints);
                this.initializeData();
            },
            initializeData() {
                // Make all the received data mutable
                this.mutableGame = this.game;
                this.mutableRound = this.round;
                this.mutablePlayer = this.player;
                if (this.playerRole) this.mutablePlayerRole = this.playerRole;
                if (this.game !== undefined && this.game.players !== undefined && this.game.players.length > 0) {
                    for (let i = 0; i < this.game.players.length; i++) {
                        this.mutablePlayers.push(this.game.players[i]);
                    }
                }
                if (this.hand !== undefined && this.hand !== null && this.hand.length > 0) {
                    for (let i = 0; i < this.hand.length; i++) {
                        let card = this.getCardById(this.hand[i]);
                        if (card) {
                            this.mutableHand.push(card);
                        }
                    }
                }
            },
            // Getters
            getCardById(id) {
                for (let i = 0; i < this.cards.length; i++) {
                    if (this.cards[i].id === id) {
                        return this.cards[i];
                    }
                }
                return false;
            },
        },
        mounted() {
            this.initialize();
        }
    }
</script>

<style lang="scss">
    #game__wrapper {
        width: 100%;
        height: 100%;
        #game {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: row;
            #game-content {
                flex: 1;
                height: 100%;
                display: flex;
                flex-direction: column;
            }
            #game-sidebar {
                display: flex;
                flex: 0 0 400px;
                flex-direction: column;
                background-color: hsl(0, 0%, 5%);
                #game-sidebar__players {
                    flex: 1;
                }
                #game-sidebar__chat {
                    flex: 0 0 300px;
                }
            }
        }
    }
</style>