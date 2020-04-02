<template>
    <div id="game-overview__wrapper">

        <!-- My game -->
        <div id="my-game" class="elevation-1" v-if="hasJoinedGame">

            <!-- Header -->
            <div id="my-game__header">
                <div id="my-game__header-left">
                    <!-- Title -->
                    <div id="my-game__title">
                        Game #{{ mutableGames[activeGameIndex].id }}
                    </div>
                </div>
                <div id="my-game__header-right">
                    <!-- Leave -->
                    <div v-if="!userIsGameMaster(mutableGames[activeGameIndex])">
                        <v-btn small text color="red" @click="onClickLeave">
                            Leave
                        </v-btn>
                    </div>
                    <!-- Delete -->
                    <div v-if="userIsGameMaster(mutableGames[activeGameIndex])">
                        <v-btn small text color="red" @click="onClickDelete">
                            Delete
                        </v-btn>
                    </div>
                </div>
            </div>

            <!-- Players -->
            <div id="my-game__players">
                <div class="player" v-for="(player, pi) in mutableGames[activeGameIndex].players" :key="pi">
                    <!-- Icon -->
                    <div class="player-icon" v-if="mutableGames[activeGameIndex].game_master_id !== player.user_id">
                        <i class="far fa-user"></i>
                    </div>
                    <div class="player-icon" v-if="mutableGames[activeGameIndex].game_master_id === player.user_id">
                        <i class="fas fa-crown"></i>
                    </div>
                    <!-- Name -->
                    <div class="player-name">{{ player.user.username }}</div>
                </div>
            </div>

            <!-- Actions -->
            <div id="my-game__actions">
                <!-- Start -->
                <div v-if="userIsGameMaster(mutableGames[activeGameIndex])">
                    <v-btn depressed color="success" :disabled="startButtonDisabled" @click="onClickStart">
                        Start game!
                    </v-btn>
                </div>
                <!-- Waiting for start -->
                <div v-if="!userIsGameMaster(mutableGames[activeGameIndex])">
                    Waiting for game master to start the game.
                </div>
            </div>

        </div>

        <!-- Game overview -->
        <div id="game-overview" class="elevation-1">
            
            <!-- Header -->
            <div id="game-overview__header">
                <div id="game-overview__header-left">
                    <!-- Title -->
                    <div id="header-title">
                        Open & ongoing games
                    </div>
                </div>
                <div id="game-overview__header-right" v-if="!hasJoinedGame">
                    <!-- Create -->
                    <v-btn small depressed color="primary" @click="onClickCreate">
                        Start a new game
                    </v-btn>
                </div>
            </div>

            <!-- Games -->
            <div id="game-overview__games" v-if="mutableGames.length > 0">
                <div id="game-overview__games-headings">
                    <div class="games-heading">ID</div>
                    <div class="games-heading">Players</div>
                    <div class="games-heading">Status</div>
                    <div class="games-heading">Actions</div>
                </div>
                <div class="game" v-for="(game, gi) in mutableGames" :key="gi">
                    <div class="game-id">
                        Game #{{ game.id }}
                    </div>
                    <div class="game-players">
                        {{ game.players.length }} {{ getPlayerText(game.players.length) }}
                    </div>
                    <div class="game-status">
                        <div class="game-status-pill">
                            {{ game.status }}
                        </div>
                    </div>
                    <div class="game-actions">
                        <v-btn small depressed color="success" @click="onClickJoin(gi)" :disabled="hasJoinedGame">
                            Join
                        </v-btn>
                    </div>
                </div>
            </div>

            <!-- No games -->
            <div id="game-overview__no-games" v-if="mutableGames.length === 0">
                There are no open or ongoing games
            </div>
        
        </div>

    </div>
</template>

<script>
    export default {
        props: [
            "user",
            "games",
            "createApiEndpoint",
            "deleteApiEndpoint",
            "joinApiEndpoint",
            "leaveApiEndpoint",
            "startApiEndpoint",
        ],
        data: () => ({
            tag: "[game-overview]",
            mutableGames: [],
            activeGameIndex: null,
        }),
        computed: {
            hasJoinedGame() {
                return this.activeGameIndex !== null;
            },
            startButtonDisabled() {
                if (this.activeGameIndex !== null && this.mutableGames[this.activeGameIndex] !== undefined) {
                    return this.mutableGames[this.activeGameIndex].players.length < 2;
                }
                return true;
            },
        },
        methods: {
            initialize() {
                console.log(this.tag+" initializing");
                console.log(this.tag+" user: ", this.user);
                console.log(this.tag+" games: ", this.games);
                console.log(this.tag+" create api endpoint: ", this.createApiEndpoint);
                console.log(this.tag+" delete api endpoint: ", this.deleteApiEndpoint);
                console.log(this.tag+" join api endpoint: ", this.joinApiEndpoint);
                console.log(this.tag+" leave api endpoint: ", this.leaveApiEndpoint);
                console.log(this.tag+" start api endpoint: ", this.startApiEndpoint);
                this.initializeData();
                this.startListening();
            },
            initializeData() {
                if (this.games !== undefined && this.games !== null && this.games.length > 0) {
                    for (let i = 0; i < this.games.length; i++) {
                        this.mutableGames.push(this.games[i]);
                        if (this.activeGameIndex === null && this.userHasJoinedGame(this.games[i])) {
                            this.activeGameIndex = i;
                        }
                    }
                }
            },
            startListening() {
                
                // Start listening on the lobby's channel
                Echo.private("lobby")

                    // Game created event
                    .listen("Game\\GameCreated", function(e) {
                        console.log(this.tag+" [event] game created", e);
                        // Add game to list of mutable games
                        let game = e.game;
                        game.players = [e.player];
                        this.mutableGames.push(game);
                        // Toast message
                        this.$toasted.show(e.user.username+" created a new game!", { duration: 3000 });
                    }.bind(this))

                    // Game deleted event
                    .listen("Game\\GameDeleted", function(e) {
                        console.log(this.tag+" [event] game deleted", e);
                        // Grab game's index by it's ID
                        let gameIndex = this.findGameIndexById(e.game_id);
                        if (gameIndex !== false) {
                            // Remove the game from the list of games
                            this.mutableGames.splice(gameIndex, 1);
                            // If this was the player's current game, reset current game
                            if (this.activeGameIndex === gameIndex) this.activeGameIndex = null;
                            // Toast message
                            this.$toasted.show(e.user.username+" deleted game #"+e.game_id, { duration: 3000 });
                        }
                    }.bind(this))

                    // Game started event
                    .listen("Game\\GameStarted", function(e) {
                        console.log(this.tag+" [event] game started", e);
                        // If this is the player's current game, redirect the user to the started game
                        
                        // Toast message
                        this.$toasted.show("Game #"+e.game.id+" has started!", { duration: 3000 });
                    }.bind(this))

                    // Player joined game event
                    .listen("Game\\PlayerJoinedGame", function(e) {
                        console.log(this.tag+" [event] player joined game", e);
                        // Add player to game's list of players
                        let gameIndex = this.findGameIndexById(e.game.id);
                        if (gameIndex !== false) this.mutableGames[gameIndex].players.push(e.player);
                        // Toast message
                        this.$toasted.show(e.user.username+" joined Game #"+e.game.id, { duration: 3000 });
                    }.bind(this))

                    // Player left game event
                    .listen("Game\\PlayerLeftGame", function(e) {
                        console.log(this.tag+" [event] player left game", e);
                        // Grab the index of the game that the player left
                        let gameIndex = this.findGameIndexById(e.game.id);
                        if (gameIndex !== false) {
                            // Grab the index of the player that left the game
                            let playerIndex = this.findPlayerIndexById(gameIndex, e.player_id);
                            if (playerIndex !== false) {
                                // Extract the player's username
                                let username = this.mutableGames[gameIndex].players[playerIndex].username;
                                // Remove the player from the game's list of player
                                this.mutableGames[gameIndex].players.splice(playerIndex, 1);
                                // Toast message
                                this.$toasted.show(username+" left Game #"+e.game.id, { duration: 3000 });
                            }
                        }
                    }.bind(this));

            },
            userHasJoinedGame(game) {
                if (game.players.length > 0) {
                    for (let i = 0; i < game.players.length; i++) {
                        if (game.players[i].user_id == this.user.id) {
                            return true;
                        }
                    }
                }
                return false;
            },
            userIsGameMaster(game) {
                return game.game_master_id === this.user.id;
            },
            onClickCreate() {
                console.log(this.tag+" clicked create button");

                // Make API request
                this.axios.post(this.createApiEndpoint)
                    .then(function(response) {
                        console.log(this.tag+" create request succeeded", response.data);
                        if (response.data.status === "success") {
                            console.log(this.tag+" create operation succeeded");
                            this.mutableGames.push(response.data.game);
                            this.activeGameIndex = this.mutableGames.length - 1;
                        } else {
                            console.warn(this.tag+" create operation failed: ", response.data.error);
                        }
                    }.bind(this))
                    .catch(function(error) {
                        console.warn(this.tag+" create request failed", error);
                    }.bind(this));

            },
            onClickDelete() {
                console.log(this.tag+" clicked delete button");

                // Compose payload
                let payload = new FormData();
                payload.append("game_id", this.mutableGames[this.activeGameIndex].id);

                // Make API request
                this.axios.post(this.deleteApiEndpoint, payload)
                    .then(function(response) {
                        console.log(this.tag+" delete request succeeded", response.data);
                        if (response.data.status === "success") {
                            console.log(this.tag+" delete operation succeeded");
                            this.mutableGames.splice(this.activeGameIndex, 1);
                            this.activeGameIndex = null;
                        } else {
                            console.warn(this.tag+" delete operation failed: ", response.data.error);
                        }
                    }.bind(this))
                    .catch(function(error) {
                        console.warn(this.tag+" delete request failed", error);
                    }.bind(this));

            },
            onClickJoin(index) {
                console.log(this.tag+" clicked join button", index);

                // Compose payload
                let payload = new FormData();
                payload.append("game_id", this.mutableGames[index].id);

                // Make API request
                this.axios.post(this.joinApiEndpoint, payload)
                    .then(function(response) {
                        console.log(this.tag+" join request succeeded", response.data);
                        if (response.data.status === "success") {
                            console.log(this.tag+" join operation succeeded", response.data.player);
                            // Save player to the list of players on the game
                            this.mutableGames[index].players.push(response.data.player);
                            // Save the game as the currently active game
                            this.activeGameIndex = index;
                        } else {
                            console.warn(this.tag+" join operation failed: ", response.data.error);
                        }
                    }.bind(this))
                    .catch(function(error) {
                        console.warn(this.tag+" join request failed", error);
                    }.bind(this));

            },
            onClickLeave() {
                console.log(this.tag+" clicked leave button");
                
                // Compose payload
                let payload = new FormData();
                payload.append("game_id", this.mutableGames[this.activeGameIndex].id);

                // Make API request
                this.axios.post(this.leaveApiEndpoint, payload)
                    .then(function(response) {
                        console.log(this.tag+" leave request succeeded", response.data);
                        if (response.data.status === "success") {
                            console.log(this.tag+" leave operation succeeded");
                            // Determine player's index in the game's list of player and remove it
                            for (let i = 0; i < this.mutableGames[this.activeGameIndex].players.length; i++) {
                                if (this.mutableGames[this.activeGameIndex].players[i].user.id === this.user.id) {
                                    this.mutableGames[this.activeGameIndex].players.splice(i, 1);
                                    break;
                                }
                            }
                            // Set active game's index to null
                            this.activeGameIndex = null;
                        } else {
                            console.warn(this.tag+" leave operation failed: ", response.data.error);
                        }
                    }.bind(this))
                    .catch(function(error) {
                        console.warn(this.tag+" leave request failed", error);
                    }.bind(this));
                
            },
            onClickStart() {
                console.log(this.tag+" clicked start button");

            },
            findGameIndexById(id) {
                for (let i = 0; i < this.mutableGames.length; i++) {
                    if (this.mutableGames[i].id === id) {
                        return i;
                    }
                }
                return false;
            },
            findPlayerIndexById(gameIndex, id) {
                for (let i = 0; i < this.mutableGames[gameIndex].players.length; i++) {
                    if (this.mutableGames[gameIndex].players[i].id === id) {
                        return i;
                    }
                }
                return false;
            },
            getPlayerText(numPlayers) {
                return numPlayers === 1 ? "player" : "players";
            },
        },
        mounted() {
            this.initialize();
        }
    }
</script>

<style lang="scss">
    #game-overview__wrapper {
        #my-game {
            overflow: hidden;
            border-radius: 3px;
            margin: 0 0 30px 0;
            background-color: #fff;
            #my-game__header {
                display: flex;
                padding: 15px 20px;
                flex-direction: row;
                box-sizing: border-box;
                border-bottom: 1px solid rgba(0, 0, 0, 0.1);
                #my-game__header-left {
                    flex: 1;
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                    #my-game__title {
                        font-size: 1.2em;
                        font-weight: 500;
                    }
                }
                #my-game__header-right {
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                }
            }
            #my-game__players {
                border-bottom: 1px solid rgba(0, 0, 0, 0.1);
                .player {
                    display: flex;
                    padding: 15px 20px;
                    flex-direction: row;
                    box-sizing: border-box;
                    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                    &:last-child {
                        border-bottom: 0;
                    }
                    .player-icon {
                        display: flex;
                        flex: 0 0 30px;
                        margin: 0 15px 0 0;
                        flex-direction: row;
                        align-items: center;
                    }
                    .player-name {
                        flex: 1;
                        display: flex;
                        flex-direction: row;
                        align-items: center;
                    }
                }
            }
            #my-game__actions {
                display: flex;
                padding: 15px 20px;
                flex-direction: row;
                align-items: center;
                box-sizing: border-box;
                justify-content: flex-end;
                background-color: hsl(0, 0%, 95%);
                .v-btn {
                    margin: 0 0 0 15px;
                }
            }
        }
        #game-overview {
            overflow: hidden;
            border-radius: 3px;
            margin: 0 0 30px 0;
            background-color: #fff;
            #game-overview__header {
                display: flex;
                padding: 15px 20px;
                flex-direction: row;
                box-sizing: border-box;
                border-bottom: 1px solid rgba(0, 0, 0, 0.1);
                #game-overview__header-left {
                    flex: 1;
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                    #header-title {
                        font-size: 1.2em;
                        font-weight: 500;
                    }
                }
                #game-overview__header-right {
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                    .v-btn {
                        margin: 0;
                    }
                }
            }
            #game-overview__games {
                #game-overview__games-headings {
                    display: flex;
                    padding: 15px 20px;
                    flex-direction: row;
                    box-sizing: border-box;
                    background-color: hsl(0, 0%, 95%);
                    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
                    .games-heading {
                        flex: 1;
                        display: flex;
                        font-weight: bold;
                        flex-direction: row;
                        align-items: center;
                        &:last-child {
                            justify-content: flex-end;
                        }
                        &:nth-child(2), &:nth-child(3) {
                            justify-content: center;
                        }
                    }
                }
                .game {
                    display: flex;
                    padding: 15px 20px;
                    flex-direction: row;
                    align-items: center;
                    box-sizing: border-box;
                    border-bottom: 1px solid rgba(0, 0, 0, .1);
                    &:last-child {
                        border-bottom: 0;
                    }
                    .game-id {
                        flex: 1;
                        display: flex;
                        flex-direction: row;
                        align-items: center;
                    }
                    .game-players {
                        flex: 1;
                        display: flex;
                        flex-direction: row;
                        align-items: center;
                        box-sizing: border-box;
                        justify-content: center;
                    }
                    .game-status {
                        flex: 1;
                        display: flex;
                        flex-direction: row;
                        align-items: center;
                        justify-content: center;
                        .game-status-pill {
                            color: #000;
                            font-size: .8em;
                            padding: 3px 6px;
                            border-radius: 3px;
                            box-sizing: border-box;
                            background-color: #ddd;
                        }
                    }
                    .game-actions {
                        flex: 1;
                        display: flex;
                        flex-direction: row;
                        align-items: center;
                        justify-content: flex-end;
                    }
                }
            }
            #game-overview__no-games {
                padding: 15px 25px;
                box-sizing: border-box;
            }
        }
    }
</style>