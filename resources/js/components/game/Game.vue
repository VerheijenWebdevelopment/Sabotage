<template>
    <div id="game">

        <!-- Content -->
        <div id="game-content">

            <!-- Role selection UI -->
            <div id="role-selection" v-if="mutableGame !== null && phase === 'role_selection'">

                <!-- Role assigned -->
                <div id="role-assigned" v-if="mutablePlayerRole !== null">

                    <!-- Title -->
                    <h1>Role assigned</h1>

                    <!-- Role card -->
                    <div class="role-card no-hover">
                        <div class="role-card__label">{{ mutablePlayerRole.label }}</div>
                    </div>

                    <!-- Please wait text -->
                    <div id="role-selection__text" v-if="playerAtPlay">
                        {{ playerAtPlay.user.username }} is currently picking a role.
                    </div>

                </div>

                <!-- Role not assigned yet -->
                <div id="role-not-assigned" v-if="mutablePlayerRole === null">

                    <!-- Title -->
                    <h1>Role selection</h1>

                    <!-- Please wait on your turn text -->
                    <div id="role-selection__text" v-if="!myTurn && playerAtPlay">
                        {{ playerAtPlay.user.username }} is currently picking a role.    
                    </div>

                    <!-- Available roles -->
                    <div id="available-roles">
                        <!-- Title -->
                        <h3>Available roles</h3>
                        <!-- Roles -->
                        <div id="available-roles__list" v-if="mutableGame.available_roles.length > 0">
                            <div class="available-role__wrapper" v-for="(role, ri) in mutableGame.available_roles" :key="ri">
                                <div class="available-role">
                                    <div class="role-name">{{ getRoleLabelById(role.role_id) }}</div>
                                    <div class="role-amount">x{{ role.count }}</div>
                                </div>
                            </div>
                        </div>
                        <!-- No roles -->
                        <div id="no-roles-available" v-if="mutableGame.available_roles.length === 0">
                            No roles available, this shouldn't happen
                        </div>
                    </div>

                    <!-- Select card -->
                    <div id="role-cards" v-if="myTurn && !selectedRoleCard">
                        <!-- Title -->
                        <h2>Role cards</h2>
                        <h3>Please choose one of the available role cards</h3>
                        <!-- Cards -->
                        <div id="role-cards__list" v-if="!selectedRoleCard">
                            <div class="role-card__wrapper" v-for="(n, ni) in mutableGame.num_available_roles" :key="ni">
                                <div class="role-card" @click="onClickRoleCard(ni)">
                                    <div class="role-card__title">Role card</div>
                                    <div class="role-card__number">#{{ n }}</div>
                                    <div class="role-card__select">
                                        Select card
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card selected & loading -->
                    <div id="role-card__selected" v-if="selectedRoleCard && selectRole.loading">
                        <!-- Title -->
                        <h2>Role card selected</h2>
                        <!-- Content -->
                        <div id="role-card__selected-content">
                            <!-- Selected card -->
                            <!-- <div id="role-card__selected-card">
                                <div id="selected-card__title">Role card</div>
                                <div id="selected-card__number"># {{ selectedRoleCard }}</div>
                            </div> -->
                            <!-- Text -->
                            <div id="role-card__selected-text">
                                <div id="selected-text__loading">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                                <div id="selected-text__title">Loading</div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Game UI -->
            <div id="game-ui" v-if="mutableGame !== null && phase === 'main'">

                <!-- Game board -->
                <div id="board-area">

                    <!-- Game info -->
                    <div id="game-info">
                        <!-- Current round -->
                        <div id="game-info__current-round">Round {{ round }}</div>
                        <!-- Current player at play -->
                        <div id="game-info__player-turn" v-if="playerAtPlay">
                            <div id="my-turn" v-if="myTurn">
                                It's your turn!
                            </div>
                            <div id="not-my-turn" v-if="!myTurn">
                                It's <span>{{ playerAtPlay.user.username }}</span>'s turn
                            </div>
                        </div>
                    </div>

                    <!-- Select gold location mode -->
                    <div id="action-mode__wrapper" v-if="modes.select_gold_location.enabled">
                        <div id="action-mode">
                            Please select the gold location you wish to reveal
                        </div>
                    </div>

                    <!-- Select tunnel mode -->
                    <div id="action-mode__wrapper" v-if="modes.select_tunnel.enabled">
                        <div id="action-mode">
                            Please select the tunnel you want to destroy
                        </div>
                    </div>

                    <!-- Select tile mode -->
                    <div id="action-mode__wrapper" v-if="modes.select_tile.enabled">
                        <div id="action-mode">
                            Please select the tile where you want to place your tunnel
                        </div>
                    </div>

                    <!-- Game board -->
                    <div id="game-board__wrapper">
                        <game-board
                            v-model="mutableBoard"
                            :cards="cards"
                            @clicked-tile="onClickedBoardTile">
                        </game-board>
                    </div>

                </div>

                <!-- Action area -->
                <div id="action-area">

                    <!-- My role -->
                    <div id="my-role">
                        <!-- Title -->
                        <div id="my-role__title">My role</div>
                        <!-- Role card -->
                        <div id="my-role__card">
                            <div id="my-role__card-text">
                                {{ mutablePlayerRole.label }}
                            </div>
                        </div>
                    </div>

                    <!-- My cards -->
                    <div id="my-hand">
                        <!-- Title -->
                        <div id="my-hand__title">My hand</div>
                        <!-- Cards -->
                        <div id="my-hand__cards" v-if="mutableHand.length > 0">
                            <div class="my-hand__card" v-for="(card, ci) in mutableHand" :key="ci">
                                <div class="my-hand__card-image" :style="{ backgroundImage: 'url('+card.image_url+')' }" @click="onClickHandCard(ci)"></div>
                            </div>
                        </div>
                        <!-- No cards in your hand -->
                        <div id="my-hand__no-cards" v-if="mutableHand.length === 0">
                            No cards in your hand at the moment
                        </div>
                    </div>

                    <!-- The deck -->
                    <div id="deck">
                        <div id="deck__title">Deck</div>
                        <div id="deck__card">
                            <div id="deck__card-text">
                                {{ mutableGame.num_cards_in_deck }}
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Rewards UI -->
            <div id="rewards-ui" v-if="mutableGame !== null && phase === 'rewards'">
                Rewards
            </div>

        </div>

        <!-- Sidebar -->
        <div id="game-sidebar">

            <!-- Players -->
            <div id="game-players__wrapper">
                <game-players
                    :game="game"
                    :player="player"
                    :player-at-play="playerAtPlay"
                    v-model="mutablePlayers"
                    :cart-icon-url="cartIconUrl"
                    :light-icon-url="lightIconUrl"
                    :pickaxe-icon-url="pickaxeIconUrl"
                    :gold-icon-url="goldIconUrl">
                </game-players>
            </div>

            <!-- Chat -->
            <div id="game-chat__wrapper">
                <game-chat
                    :game="game"
                    :player="player"
                    :send-message-api-endpoint="sendMessageApiEndpoint">
                </game-chat>
            </div>

        </div>

        <!-- View card dialog -->
        <v-dialog v-model="dialogs.view_card.show" width="800">
            <div class="dialog" v-if="this.dialogs.view_card.index !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="dialogs.view_card.show = false">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <h3 class="dialog-title">View card</h3>
                    <!-- Text -->
                    <div class="dialog-text nm">
                        <!-- Card information -->
                        <div class="card-info">
                            <div class="card-info__card" :class="{ inverted: dialogs.view_card.inverted }" :style="{ backgroundImage: 'url('+viewCardDialogCard.image_url+')' }"></div>
                            <div class="card-info__content">
                                <!-- Description -->
                                <div class="card-info__description">
                                    <div class="card-info__description-label">Description</div>
                                    <div class="card-info__description-text" v-if="viewCardDialogCard.type === 'tunnel'">
                                        This is a tunnel card which can be placed on the board to expand the tunnel system in the desired direction.
                                    </div>
                                    <div class="card-info__description-text" v-if="viewCardDialogCard.type !== 'tunnel' && viewCardDialogCard.description === null">
                                        This card is missing a description.
                                    </div>
                                    <div class="card-info__description-text" v-if="viewCardDialogCard.type !== 'tunnel' && viewCardDialogCard.description !== null">
                                        {{ viewCardDialogCard.description }}
                                    </div>
                                </div>
                                <!-- Actions -->
                                <div class="card-info__actions">
                                    <div class="card-info__actions-text" v-if="!myTurn">
                                        You can't play this card until it's your turn
                                    </div>
                                    <div class="card-info__actions-buttons" v-if="myTurn">
                                        <v-btn class="icon-only" @click="onClickInvertCard">
                                            <i class="fas fa-sync-alt"></i>
                                        </v-btn>
                                        <v-btn @click="onClickPlayCard">
                                            Play card
                                        </v-btn>
                                        <v-btn @click="onClickFoldCard">
                                            Fold card
                                        </v-btn>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </v-dialog>

        <!-- Fold card dialog -->
        <v-dialog v-model="dialogs.fold_card.show" width="550">
            <div class="dialog" v-if="dialogs.fold_card.index !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="onClickCancelFold">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Fold card</div>
                    <!-- Text -->
                    <div class="dialog-text centered">
                        <div class="card mb-15" :style="{ backgroundImage: 'url('+foldCardDialogCard.image_url+')' }"></div>
                        Are you sure you want to fold this card?<br>
                        The card will be discarded, you will draw a new card and your turn will end.
                    </div>
                </div>
                <!-- Controls -->
                <div class="dialog-controls">
                    <div class="dialog-controls__left">
                        <v-btn text dark @click="onClickCancelFold">
                            Cancel
                        </v-btn>
                    </div>
                    <div class="dialog-controls__right">
                        <v-btn dark color="red" @click="onClickConfirmFold" :loading="dialogs.fold_card.loading">
                            Fold card
                        </v-btn>
                    </div>
                </div>
            </div>
        </v-dialog>

        <!-- Confirm enlighten gold location dialog -->
        <v-dialog v-model="dialogs.confirm_gold_location.show" width="800">
            <div class="dialog" v-if="dialogs.confirm_gold_location.card_index !== null && dialogs.confirm_gold_location.gold_location !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="onClickCancelEnlighten">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Reveal gold location contents</div>
                    <!-- Text -->
                    <div class="dialog-text">
                        {{ revealGoldLocationText }}<br>
                        Are you sure you want to reveal the contents of this gold location?
                    </div>
                </div>
                <!-- Controls -->
                <div class="dialog-controls">
                    <div class="dialog-controls__left">
                        <v-btn text dark @click="onClickCancelEnlighten">
                            Cancel
                        </v-btn>
                        <v-btn text dark @click="onClickRetryEnlighten">
                            Select another location
                        </v-btn>
                    </div>
                    <div class="dialog-controls__right">
                        <v-btn dark @click="onClickConfirmEnlighten" :loading="dialogs.confirm_gold_location.loading">
                            Reveal contents
                        </v-btn>
                    </div>
                </div>
            </div>
        </v-dialog>

        <!-- Reveal gold location dialog -->
        <v-dialog v-model="dialogs.reveal_gold_location.show" width="500">
            <div class="dialog" v-if="dialogs.reveal_gold_location.gold_location !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="dialogs.reveal_gold_location.show = false">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Gold Location {{ dialogs.reveal_gold_location.gold_location }}</div>
                    <!-- Does contain gold text -->
                    <div class="dialog-text" v-if="dialogs.reveal_gold_location.contains_gold">
                        <div id="reveal-gold-location">
                            <div id="reveal-gold-location__image" :style="{ backgroundImage: 'url('+goldBarsIconUrl+')' }"></div>
                            <div id="reveal-gold-location__text">
                                This location contains the gold!
                            </div>
                        </div>
                    </div>  
                    <!-- Does not contain gold text -->
                    <div class="dialog-text" v-if="!dialogs.reveal_gold_location.contains_gold">
                        <div id="reveal-gold-location">
                            <div id="reveal-gold-location__image" :style="{ backgroundImage: 'url('+coalIconUrl+')' }"></div>
                            <div id="reveal-gold-location__text">
                                This location does not contain any gold.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </v-dialog>

        <!-- Confirm sabotage player dialog -->
        <v-dialog v-model="dialogs.confirm_sabotage_player.show" width="800">
            <div class="dialog" v-if="dialogs.confirm_sabotage_player.card_index !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="onClickCancelSabotagePlayer">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Sabotage player's tool</div>
                    <!-- Select player -->
                    <div class="select-player">
                        <div class="select-player__title">Select target player</div>
                        <div class="select-player__list">
                            <div class="select-player__list-item" v-for="(player, pi) in mutablePlayers" :key="pi" v-if="player.id !== mutablePlayer.id">
                                <div class="player-option" :class="{ selected: dialogs.confirm_sabotage_player.player_id === player.id }" @click="onClickSelectPlayer('sabotage', player.id)">
                                    {{ player.user.username }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Select tool -->
                </div>
                <!-- Controls -->
                <div class="dialog-controls">
                    <div class="dialog-controls__left">
                        <v-btn text dark @click="onClickCancelSabotagePlayer">
                            Cancel
                        </v-btn>
                    </div>
                    <div class="dialog-controls__right">
                        <v-btn dark @click="onClickConfirmSabotagePlayer" :loading="dialogs.confirm_sabotage_player.loading" :disabled="dialogs.confirm_sabotage_player.player_id === null">
                            Sabotage player's tool
                        </v-btn>
                    </div>
                </div>
            </div>
        </v-dialog>

        <!-- Confirm recover player dialog -->
        <v-dialog v-model="dialogs.confirm_recover_player.show" width="800">
            <div class="dialog" v-if="dialogs.confirm_recover_player.card_index !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="onClickCancelRecoverPlayer">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Recover player's tool</div>
                    <!-- Select player -->
                    <div class="select-player">
                        <div class="select-player__title">Select target player</div>
                        <div class="select-player__list">
                            <div class="select-player__list-item" v-for="(player, pi) in mutablePlayers" :key="pi">
                                <div class="player-option" :class="{ selected: dialogs.confirm_recover_player.player_id === player.id }" @click="onClickSelectPlayer('recover', player.id)">
                                    {{ player.user.username }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Select tool -->
                    <div class="select-tool" v-if="showRecoverToolSelection">
                        <div class="select-tool__title">Select tool to recover</div>
                        <div class="select-tool__list">

                        </div>
                    </div>
                </div>
                <!-- Controls -->
                <div class="dialog-controls">
                    <div class="dialog-controls__left">
                        <v-btn text dark @click="onClickCancelRecoverPlayer">
                            Cancel
                        </v-btn>
                    </div>
                    <div class="dialog-controls__right">
                        <v-btn dark @click="onClickConfirmRecoverPlayer" :loading="dialogs.confirm_recover_player.loading" :disabled="dialogs.confirm_recover_player.player_id === null">
                            Recover player's tool
                        </v-btn>
                    </div>
                </div>
            </div>
        </v-dialog>

        <!-- Confirm collapse tunnel dialog -->
        <v-dialog v-model="dialogs.confirm_collapse_tunnel.show" width="800">
            <div class="dialog" v-if="dialogs.confirm_collapse_tunnel.card_index !== null && dialogs.confirm_collapse_tunnel.tunnel_coordinates !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="onClickCancelCollapseTunnel">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Collapse tunnel</div>
                    <!-- Text -->
                    <div class="dialog-text">
                        Are you sure you want to collapse the tunnel on {{ dialogs.confirm_collapse_tunnel.tunnel_coordinates.x+":"+dialogs.confirm_collapse_tunnel.tunnel_coordinates.y }}?
                    </div>
                </div>
                <!-- Controls -->
                <div class="dialog-controls">
                    <div class="dialog-controls__left">
                        <v-btn text dark @click="onClickCancelCollapseTunnel">
                            Cancel
                        </v-btn>
                    </div>
                    <div class="dialog-controls__right">
                        <v-btn dark @click="onClickConfirmCollapseTunnel" :loading="dialogs.confirm_collapse_tunnel.loading" :disabled="dialogs.confirm_collapse_tunnel.player_id === null">
                            Collapse tunnel
                        </v-btn>
                    </div>
                </div>
            </div>
        </v-dialog>

        <!-- Confirm place tunnel dialog -->
        <v-dialog v-model="dialogs.confirm_place_tunnel.show" width="800">
            <div class="dialog" v-if="dialogs.confirm_place_tunnel.card_index !== null && dialogs.confirm_place_tunnel.tunnel_coordinates !== null">
                <!-- Close button -->
                <div class="dialog__close-button" @click="onClickCancelPlaceTunnel">
                    <i class="fas fa-times"></i>
                </div>
                <!-- Content -->
                <div class="dialog-content">
                    <!-- Title -->
                    <div class="dialog-title">Place tunnel card</div>
                    <!-- Place tunnel -->
                    <div id="place-tunnel">
                        <div id="place-tunnel__preview">
                            <div id="preview">
                                <div class="preview-row" v-for="(row, ri) in dialogs.confirm_place_tunnel.preview" :key="ri">
                                    <div class="preview-col" v-for="(col, ci) in dialogs.confirm_place_tunnel.preview[ri]" :key="ci">
                                        <div class="preview-card" v-if="col !== null" :style="{ backgroundImage: 'url('+getCardImageById(col.card_id)+')' }"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="place-tunnel__text">
                            Are you sure you want to place your card on the selected coordinates ({{ dialogs.confirm_place_tunnel.tunnel_coordinates.x+","+dialogs.confirm_place_tunnel.tunnel_coordinates.y }})?
                        </div>
                    </div>
                </div>
                <!-- Controls -->
                <div class="dialog-controls">
                    <div class="dialog-controls__left">
                        <v-btn text dark @click="onClickCancelPlaceTunnel">
                            Cancel
                        </v-btn>
                    </div>
                    <div class="dialog-controls__right">
                        <v-btn dark @click="onClickConfirmPlaceTunnel" :loading="dialogs.confirm_place_tunnel.loading" :disabled="dialogs.confirm_place_tunnel.player_id === null">
                            Place tunnel
                        </v-btn>
                    </div>
                </div>
            </div>
        </v-dialog>

    </div>
</template>

<script>
    export default {
        props: [
            "game",
            "player",
            "playerRole",
            "hand",
            "roles",
            "cards",
            "sendMessageApiEndpoint",
            "performActionApiEndpoint",
            "cartIconUrl",
            "lightIconUrl",
            "pickaxeIconUrl",
            "goldIconUrl",
            "goldBarsIconUrl",
            "coalIconUrl",
        ],
        data: () => ({
            tag: "[game]",
            turn: 1,
            round: 1,
            phase: null,
            playersTurn: 1,
            mutablePlayers: [],
            mutableGame: null,
            mutablePlayer: null,
            mutablePlayerRole: null,
            mutableHand: [],
            mutableBoard: null,
            selectRole: {
                loading: false,
                selectedCardIndex: null,
            },
            dialogs: {
                view_card: {
                    show: false,
                    index: null,
                    inverted: false,
                },
                fold_card: {
                    show: false,
                    index: null,
                    loading: false,
                },
                confirm_sabotage_player: {
                    show: false,
                    card_index: null,
                    player_id: null,
                    tool: null,
                },
                confirm_recover_player: {
                    show: false,
                    player_id: null,
                    tool: null,
                },
                confirm_gold_location: {
                    show: false,
                    loading: false,
                    card_index: null,
                    gold_location: null,
                },
                confirm_collapse_tunnel: {
                    show: false,
                    loading: false,
                    card_index: null,
                    tunnel_coordinates: null,
                },
                confirm_place_tunnel: {
                    show: false,
                    loading: false,
                    card_index: null,
                    tunnel_coordinates: null,
                    inverted: false,
                    preview: [],
                },
                reveal_gold_location: {
                    show: false,
                    gold_location: null,
                    contains_gold: false,
                },
            },
            modes: {
                select_gold_location: {
                    enabled: false,
                },
                select_tunnel: {
                    enable: false,
                },
                select_tile: {
                    enable: false,
                },
            }
        }), 
        computed: {
            playerAtPlay() {
                for (let i = 0; i < this.mutablePlayers.length; i++) {
                    if (this.mutablePlayers[i].player_number == this.playersTurn) return this.mutablePlayers[i];
                }
                return false;
            },
            myTurn() {
                return this.playerAtPlay.id === this.player.id;
            },
            hasSelectedRole() {
                return this.mutablePlayerRole !== null;
            },
            numRoleCardOptions() {
                return this.mutableGame.num_available_roles - this.mutableGame.players_with_selected_roles;
            },
            selectedRoleCard() {
                if (this.selectRole.selectedCardIndex !== null) {
                    return this.selectRole.selectedCardIndex + 1;
                }
                return false;
            },
            allPlayersHaveRoles() {
                if (this.mutableGame !== null && this.mutablePlayers !== null) {
                    return this.mutableGame.players_with_selected_roles.length === this.mutablePlayers.length;
                }
                return false;
            },
            selectedHandCards() {
                let out = [];
                for (let i = 0; i < this.mutableHand.length; i++) {
                    if (this.mutableHand[i].selected) {
                        out.push(this.mutableHand[i]);
                    }
                }
                return out;
            },
            selectedHandCardIndex() {
                for (let i = 0; i < this.mutableHand.length; i++) {
                    if (this.mutableHand[i].selected) {
                        return i;
                    }
                }
                return false;
            },
            viewCardDialogCard() {
                return this.mutableHand[this.dialogs.view_card.index];
            },
            foldCardDialogCard() {
                return this.mutableHand[this.dialogs.fold_card.index];
            },
            revealGoldLocationText() {
                let out = "You have selected ";
                if (this.dialogs.confirm_gold_location.gold_location === 1) {
                    out += "the first";
                } else if (this.dialogs.confirm_gold_location.gold_location === 2) {
                    out += "the second";
                } else {
                    out += "the third"
                }
                out += " gold location.";
                return out;
            },
            showRecoverToolSelection() {
                let recoverCard = this.mutableHand[this.dialogs.confirm_recover_player.card_index];
                if (recoverCard) {
                    return recoverCard.name === "recover_pickaxe_light" || recoverCard.name === "recover_pickaxe_axe" || recoverCard.name === "recover_light_cart";
                }
                return false;
            },
            showSabotageToolSelection() {
                let recoverCard = this.mutableHand[this.dialogs.confirm_recover_player.card_index];
                if (recoverCard) {
                    return recoverCard.name === "sabotage_pickaxe_light" || recoverCard.name === "sabotage_pickaxe_axe" || recoverCard.name === "sabotage_light_cart";
                }
                return false;
            },
        },
        methods: {
            // Init
            initialize() {
                console.log(this.tag+" initializing");
                console.log(this.tag+" game: ", this.game);
                console.log(this.tag+" player: ", this.player);
                console.log(this.tag+" player role: ", this.playerRole);
                console.log(this.tag+" hand: ", this.hand);
                console.log(this.tag+" roles: ", this.roles);
                console.log(this.tag+" cards: ", this.cards);
                console.log(this.tag+" send message api endpoint: ", this.sendMessageApiEndpoint);
                console.log(this.tag+" perform action api endpoint: ", this.performActionApiEndpoint);
                console.log(this.tag+" cart icon url: ", this.cartIconUrl);
                console.log(this.tag+" light icon url: ", this.lightIconUrl);
                console.log(this.tag+" pickaxe icon url: ", this.pickaxeIconUrl);
                console.log(this.tag+" gold icon url: ", this.goldIconUrl);
                console.log(this.tag+" gold bars icon url: ", this.goldBarsIconUrl);
                console.log(this.tag+" coal icon url: ", this.coalIconUrl);
                // console.log(this.tag+" ");
                this.initializeData();
                this.startListening();
            },
            initializeData() {

                // Make the game mutable
                this.mutableGame = this.game;

                // Make current player mutable
                this.mutablePlayer = this.player;

                // Make current player's role mutable (if it has been selected, otherwise leave it at null)
                if (this.playerRole) this.mutablePlayerRole = this.playerRole;

                // Make game's players mutable
                if (this.game !== undefined && this.game !== null) {
                    for (let i = 0; i < this.game.players.length; i++) {
                        this.mutablePlayers.push(this.game.players[i]);
                    }
                }

                // Make the player's hand mutable
                if (this.hand !== undefined && this.hand !== null && this.hand && this.hand.length > 0) {
                    this.initializeHand(this.hand);
                }

                // Make the board mutable
                this.mutableBoard = this.game.board;

                // Initialize the game's state
                this.turn = this.game.turn;
                this.round = this.game.round;
                this.phase = this.game.phase;
                this.playersTurn = this.game.player_turn;

            },
            initializeHand(hand) {
                this.mutableHand = [];
                for (let i = 0; i < hand.length; i++) {
                    let card = this.getCardById(hand[i]);
                    if (card) {
                        this.mutableHand.push(card);
                    }
                }
            },
            // Events
            startListening() {
                // Connect to the game's channel
                Echo.private('game.'+this.game.id)
                    .listen('Game\\PlayerSelectedRole', this.onPlayerSelectedRole)
                    .listen('Game\\PlayerToolBlocked', this.onPlayerToolBlocked)
                    .listen('Game\\PlayerToolRecovered', this.onPlayerToolRecovered)
                    .listen('Game\\PlayerCheckedGoldLocation', this.onPlayerCheckedGoldLocation)
                    .listen('Game\\PlayerPlacedTunnel', this.onPlayerPlacedTunnel)
                    .listen('Game\\PlayerCollapsedTunnel', this.onPlayerCollapsedTunnel)
                    .listen('Game\\TurnEnded', this.onTurnEnded)
                    .listen('Game\\RoundEnded', this.onRoundEnded)
                    .listen('Game\\GameEnded', this.onGameEnded);
            },
            onPlayerSelectedRole(e) {
                console.log(this.tag+"[event] player selected role", e);

                // Add player's ID to the list of players who have selected a role
                this.mutableGame.players_with_selected_roles.push(e.player.id);
                this.mutableGame.num_available_roles -= 1;

                // Switch to the main phase when all players have selected their roles
                if (this.mutableGame.players_with_selected_roles.length === this.mutableGame.players.length) {
                    setTimeout(function() {
                        this.phase = "main";
                    }.bind(this), 2000);
                }

            },
            onPlayerToolBlocked(e) {
                console.log(this.tag+"[event] player tool blocked", e);
                // Update the target player's tool's status
                for (let i = 0; i < this.mutablePlayers.length; i++) {
                    if (this.mutablePlayers[i].id === e.target_player.id) {
                        if (e.tool === "pickaxe") {
                            this.mutablePlayers[i].pickaxe_available = 0;
                        } else if (e.tool === "light") {
                            this.mutablePlayers[i].light_available = 0;
                        } else if (e.tool === "cart") {
                            this.mutablePlayers[i].cart_available = 0;
                        }
                        break;
                    }
                }
                // Notify the player what just happened
                let player = this.getPlayerById(e.player.id);
                let targetPlayer = this.getPlayerById(e.target_player.id);
                this.$toasted.show(player.user.username+" blocked "+targetPlayer.user.username+"'s "+e.tool, { duration: 5000 });
            },
            onPlayerToolRecovered(e) {
                console.log(this.tag+"[event] player tool recovered", e);
                // Update the target player's tool's status
                for (let i = 0; i < this.mutablePlayers.length; i++) {
                    if (this.mutablePlayers[i].id === e.target_player.id) {
                        if (e.tool === "pickaxe") {
                            this.mutablePlayers[i].pickaxe_available = 1;
                        } else if (e.tool === "light") {
                            this.mutablePlayers[i].light_available = 1;
                        } else if (e.tool === "cart") {
                            this.mutablePlayers[i].cart_available = 1;
                        }
                        break;
                    }
                }
                // Notify the player what just happened
                let player = this.getPlayerById(e.player.id);
                let targetPlayer = this.getPlayerById(e.target_player.id);
                this.$toasted.show(player.user.username+" recovered "+targetPlayer.user.username+"'s "+e.tool, { duration: 5000 });
            },
            onPlayerCheckedGoldLocation(e) {
                console.log(this.tag+"[event] player checked gold location", e);
                // Notify the player what just happened
                let player = this.getPlayerById(e.player.id);
                this.$toasted.show(player.user.username+" has checked gold location number "+e.gold_location, { duration: 5000 });
            },
            onPlayerPlacedTunnel(e) {
                console.log(this.tag+"[event] player placed tunnel", e);
                // Update the board
                this.mutableBoard[e.coordinates.y][e.coordinates.x] = {
                    card_id: e.card.id,
                    inverted: e.inverted,
                };
                this.$forceUpdate();
                // Notify user wassup
                let player = this.getPlayerById(e.player.id);
                this.$toasted.show(player.user.username+" has placed a tunnel on coordinates "+e.coordinates.x+":"+e.coordinates.y, { duration: 3000 });
            },
            onPlayerCollapsedTunnel(e) {
                console.log(this.tag+"[event] player collapsed tunnel", e);
                // Update the board
                this.mutableBoard[e.coordinates.y][e.coordinates.x] = null;
                // Notify user wassup
                let player = this.getPlayerById(e.player.id);
                this.$toasted.show(player.user.username+" has placed a tunnel on coordinates "+e.coordinates.x+":"+e.coordinates.y, { duration: 3000 });
            },
            onTurnEnded(e) {
                console.log(this.tag+"[event] turn ended", e);

                // Set the number of the player who's turn it is currently
                this.playersTurn = e.game.player_turn;
                this.mutableGame.num_cards_in_deck = e.game.num_cards_in_deck;
                
            },
            onRoundEnded(e) {
                console.log(this.tag+"[event] round ended", e);

            },
            onGameEnded(e) {
                console.log(this.tag+"[event] game ended", e);

            },
            // Role selection
            onClickRoleCard(index) {
                console.log(this.tag+" clicked role card", index);

                // Save the index of the selected role card
                this.selectRole.selectedCardIndex = index;

                // Indicate we're doing stuff
                this.selectRole.loading = true;

                // Perform the action
                this.sendPerformActionRequest("selected_role_card", { index: index })

                    // If we succeeded to perform the action
                    .then(function(response) {
                        console.log(this.tag+" succeeded to select role!", response);

                        // Save the assigned role & the player's hand
                        this.mutablePlayerRole = response.data.role;
                        
                        // Initialize the player's hand
                        this.initializeHand(response.data.hand);

                        // Add player's id to the game's list of players with selected roles
                        this.mutableGame.players_with_selected_roles.push(this.mutablePlayer.id);
                        this.mutableGame.num_available_roles -= 1;

                        // Indicate we're done doing stuff
                        this.selectRole.loading = false;

                        // Switch to the main phase when all players have selected their roles
                        if (this.mutableGame.players_with_selected_roles.length === this.mutableGame.players.length) {
                            setTimeout(function() {
                                this.phase = "main";
                            }.bind(this), 2000);
                        }

                    }.bind(this))

                    // If we failed to perform the action; let's make the user be stuck here because wtf
                    .catch(function(error) {
                        console.warn(this.tag+" failed to select role!", error);
                    }.bind(this));

            },
            // Hand cards
            onClickHandCard(index) {
                console.log(this.tag+" clicked card in hand", index);
                // this.mutableHand[index].selected = !this.mutableHand[index].selected;
                this.dialogs.view_card.index = index;
                this.dialogs.view_card.show = true;
            },
            onClickInvertCard() {
                console.log(this.tag+" clicked invert card button");
                this.dialogs.view_card.inverted = !this.dialogs.view_card.inverted;
            },
            onClickFoldCard() {
                console.log(this.tag+" clicked fold card button");
                this.dialogs.fold_card.index = this.dialogs.view_card.index;
                this.dialogs.view_card.show = false;
                this.dialogs.fold_card.show = true;
            },
            onClickCancelFold() {
                console.log(this.tag+" clicked cancel fold button");
                this.dialogs.fold_card.show = false;
                this.dialogs.view_card.show = true;
            },
            onClickConfirmFold() {
                console.log(this.tag+" clicked confirm fold button");
                // Start loading
                this.dialogs.fold_card.loading = true;
                // Perform the action
                this.sendPerformActionRequest("fold_card", { index: this.selectedHandCardIndex })
                    // Action succeeded
                    .then(function(response) {
                        // Remove the card from the user's hand
                        this.mutableHand.splice(this.selectedHandCardIndex, 1);
                        // Add the drawn card to the user's hand
                        if (response.data.new_card) this.mutableHand.push(response.data.new_card);
                        // Stop loading
                        this.dialogs.fold_card.loading = false;
                        // Hide the dialog
                        this.dialogs.fold_card.show = false;
                    }.bind(this))
                    // Action failed
                    .catch(function(error) {
                        console.warn(this.tag+" failed to fold card!", error);
                        // Stop loading
                        this.dialogs.fold_card.loading = false;
                    }.bind(this));
            },
            onClickPlayCard() {
                console.log(this.tag+" clicked play card button");
                
                // Grab the card we want to play
                let card = this.mutableHand[this.dialogs.view_card.index];

                // If it's a tunnel card
                if (card.type === "tunnel") {
                    console.log(this.tag+" playing a tunnel card");
                    this.modes.select_tile.enabled = true;
                    this.dialogs.view_card.show = false;
                // If it's an action card
                } else {
                    // If it's the enlighten card
                    if (card.name === "enlighten") {
                        console.log(this.tag+" playing an enlighten card");
                        this.modes.select_gold_location.enabled = true;
                        this.dialogs.view_card.show = false;
                    }
                    // If it's the collapse card
                    else if (card.name === "collapse") {
                        console.log(this.tag+" playing a collapse card");
                        this.modes.select_tunnel.enabled = true;
                        this.dialogs.view_card.show = false;
                    }
                    // If it's a sabotage card
                    else if (card.name.includes("sabotage")) {
                        console.log(this.tag+" playing a sabotage card");
                        this.dialogs.confirm_sabotage_player.card_index = this.dialogs.view_card.index;
                        this.dialogs.view_card.show = false;
                        this.dialogs.confirm_sabotage_player.show = true;
                    }
                    // If it's a recover card
                    else if (card.name.includes("recover")) {
                        console.log(this.tag+" playing a recover card");
                        this.dialogs.confirm_recover_player.card_index = this.dialogs.view_card.index;
                        this.dialogs.view_card.show = false;
                        this.dialogs.confirm_recover_player.show = true;
                    }
                }

            },
            // Confirm enlighten gold location dialog
            onClickCancelEnlighten() {
                console.log(this.tag+" clicked cancel enlighten button");
                this.dialogs.confirm_gold_location.show = false;
            },
            onClickRetryEnlighten() {
                console.log(this.tag+" clicked retry enlighten button");
                this.dialogs.confirm_gold_location.show = false;
                this.modes.select_gold_location.enabled = true;
            },
            onClickConfirmEnlighten() {
                console.log(this.tag+" clicked confirm enlighten button");

                // Set loading to true
                this.dialogs.confirm_gold_location.loading = true;

                // Compose data for the API request
                let data = {
                    index: this.dialogs.confirm_gold_location.card_index,
                    gold_location: this.dialogs.confirm_gold_location.gold_location,
                };

                // Send API request to play the sabotage card
                this.sendPerformActionRequest("play_card", data)
                    
                    // When request succeeds
                    .then(function(response) {
                        // Update player's hand
                        this.mutableHand.splice(this.dialogs.confirm_gold_location.card_index, 1);
                        if (response.data.new_card) this.mutableHand.push(response.data.new_card);
                        // Close this dialog
                        this.dialogs.confirm_gold_location.loading = false;
                        this.dialogs.confirm_gold_location.show = false;
                        // Open up the gold location reveal dialog
                        this.dialogs.reveal_gold_location.gold_location = this.dialogs.confirm_gold_location.gold_location;
                        this.dialogs.reveal_gold_location.contains_gold = response.data.contains_gold;
                        this.dialogs.reveal_gold_location.show = true;
                    }.bind(this))

                    // When request fails
                    .catch(function(error) {
                        console.warn(this.tag+" failed to play enlighten card", error);
                        this.dialogs.confirm_gold_location.loading = false;
                    }.bind(this));

            },
            // Confirm sabotage & recover dialog
            onClickSelectPlayer(dialog, playerId) {
                console.log(this.tag+" clicked player option");
                console.log(this.tag+" dialog: ", dialog);
                console.log(this.tag+" player id: ", playerId);
                if (dialog === "recover") {
                    if (this.dialogs.confirm_recover_player.player_id === playerId) {
                        this.dialogs.confirm_recover_player.player_id = null;
                    } else {
                        this.dialogs.confirm_recover_player.player_id = playerId;
                    }
                } else if (dialog === "sabotage") {
                    if (this.dialogs.confirm_sabotage_player.player_id === playerId) {
                        this.dialogs.confirm_sabotage_player.player_id = null;
                    } else {
                        this.dialogs.confirm_sabotage_player.player_id = playerId;
                    }
                }
            },
            onClickSelectTool(dialog, tool) {

            },
            // Confirm sabotage player dialog
            onClickCancelSabotagePlayer() {
                console.log(this.tag+" clicked cancel sabotage player");
                this.dialogs.confirm_sabotage_player.show = false;
                this.dialogs.view_card.show = true;
            },
            onClickConfirmSabotagePlayer() {
                console.log(this.tag+" clicked confirm sabotage player");

                // Set loading to true
                this.dialogs.confirm_sabotage_player.loading = true;

                // Compose data for the API request
                let data = {
                    index: this.dialogs.confirm_sabotage_player.card_index,
                    player_id: this.dialogs.confirm_sabotage_player.player_id,
                    tool: null,
                };

                // Grab the card we're about to play
                let card = this.mutableHand[this.dialogs.confirm_sabotage_player.card_index];
                console.log(this.tag+" card: ", card);

                // Determine the tool we're blocking (and add it to the API payload if we're dealing with a multiple choice card)
                let tool = null;
                if (card.name === "sabotage_pickaxe_cart" || card.name === "sabotage_pickaxe_light" || card.name === "sabotage_light_cart") {
                    tool = this.dialogs.confirm_sabotage_player.tool;
                    data.tool = tool;
                } else {
                    if (card.name === "sabotage_light") {
                        tool = "light";
                    } else if (card.name === "sabotage_cart") {
                        tool = "cart";
                    } else {
                        tool = "pickaxe";
                    }
                }

                console.log(this.tag+" tool: ", tool);

                // Send API request to play the sabotage card
                this.sendPerformActionRequest("play_card", data)
                    
                    // When request succeeds
                    .then(function(response) {
                        // Update the player's hand
                        this.mutableHand.splice(this.dialogs.confirm_sabotage_player.card_index, 1);
                        if (response.data.new_card) this.mutableHand.push(response.data.new_card);
                        // Update the target player
                        for (let i = 0; i < this.mutablePlayers.length; i++) {
                            if (this.mutablePlayers[i].id === data.player_id) {
                                if (tool === "pickaxe") {
                                    this.mutablePlayers[i].pickaxe_available = false;
                                } else if (tool === "light") {
                                    this.mutablePlayers[i].light_available = false;
                                } else {
                                    this.mutablePlayers[i].cart_available = false;
                                }
                                break;
                            }
                        }
                        // Hide the dialog
                        this.dialogs.confirm_sabotage_player.loading = false;
                        this.dialogs.confirm_sabotage_player.show = false;
                    }.bind(this))

                    // When request fails
                    .catch(function(error) {
                        console.warn(this.tag+" failed to play sabotage card", error);
                        this.dialogs.confirm_sabotage_player.loading = false;
                    }.bind(this));

            },
            // Confirm recover player dialog
            onClickCancelRecoverPlayer() {
                console.log(this.tag+" clicked cancel recover player");
                this.dialogs.confirm_recover_player.show = false;
                this.dialogs.view_card.show = true;
            },
            onClickConfirmRecoverPlayer() {
                console.log(this.tag+" clicked confirm recover player");
                
                // Set loading to true
                this.dialogs.confirm_recover_player.loading = true;

                // Compose data for the API request
                let data = {
                    index: this.dialogs.confirm_recover_player.card_index,
                    player_id: this.dialogs.confirm_recover_player.player_id,
                };

                // Grab the card we're about to play
                let card = this.mutableHand[this.dialogs.confirm_recover_player.card_index];
                console.log("card id: ", this.dialogs.confirm_recover_player.card_index);
                console.log("card: ", card);
                
                // Determine the tool we're blocking (and add it to the API payload if we're dealing with a multiple choice card)
                let tool = "pickaxe";
                if (card.name === "recover_pickaxe_cart" || card.name === "recover_pickaxe_light" || card.name === "recover_light_cart") {
                    let tool = this.dialogs.confirm_recover_player.tool;
                    data.tool = tool;
                } else {
                    if (card.name === "recover_light") {
                        tool = "light";
                    } else if (card.name === "recover_cart") {
                        tool = "cart";
                    }
                }

                // Send API request to play the sabotage card
                this.sendPerformActionRequest("play_card", data)
                    
                    // When request succeeds
                    .then(function(response) {

                        // Update player's hand
                        this.mutableHand.splice(this.dialogs.confirm_recover_player.card_index, 1);
                        if (response.data.new_card) this.mutableHand.push(response.data.new_card);
                        
                        // Update target player's tool's status
                        for (let i = 0; i < this.mutablePlayers.length; i++) {
                            if (this.mutablePlayers[i].id === data.player_id) {
                                if (tool === "pickaxe") {
                                    this.mutablePlayers[i].pickaxe_available = true;
                                } else if (tool === "light") {
                                    this.mutablePlayers[i].light_available = true;
                                } else {
                                    this.mutablePlayers[i].cart_available = true;
                                }
                                break;
                            }
                        }

                        // Hide the dialog
                        this.dialogs.confirm_recover_player.loading = false;
                        this.dialogs.confirm_recover_player.show = false;

                    }.bind(this))

                    // When request fails
                    .catch(function(error) {
                        console.warn(this.tag+" failed to play sabotage card", error);
                        this.dialogs.confirm_recover_player.loading = false;
                    }.bind(this));

            },
            // Confirm collapse tunnel dialog
            onClickCancelCollapseTunnel() {
                console.log(this.tag+" clicked cancel collapse tunnel");
                this.dialogs.confirm_collapse_tunnel.show = false;
            },
            onClickConfirmCollapseTunnel() {
                console.log(this.tag+" clicked confirm collapse tunnel");

            },
            // Confirm place tunnel dialog
            onClickCancelPlaceTunnel() {
                console.log(this.tag+" clicked cancel place tunnel");
                this.dialogs.confirm_place_tunnel.show = false;
                this.dialogs.view_card.show = true; // because maybe we just want to invert the tunnel card before retrying
            },
            onClickConfirmPlaceTunnel() {
                console.log(this.tag+" clicked confirm place tunnel");

                this.dialogs.confirm_place_tunnel.loading = true;

                // Compose data to send along with the API request
                let data = {
                    index: this.dialogs.confirm_place_tunnel.card_index,
                    target_coordinates: this.dialogs.confirm_place_tunnel.tunnel_coordinates,
                    inverted: this.dialogs.confirm_place_tunnel.inverted,
                };

                // Send API request
                this.sendPerformActionRequest("play_card", data)
                    
                    .then(function(response) {
                        console.log(this.tag+" operation succeeded");
                        
                        // Grab card from player's hand
                        let card = this.mutableHand[this.dialogs.confirm_place_tunnel.card_index];
                        console.log(this.tag+" card: ", card);

                        // Update the game board
                        let coords = this.dialogs.confirm_place_tunnel.tunnel_coordinates;
                        console.log(this.tag+" coords: ", coords);
                        this.mutableBoard[coords.y][coords.x] = {
                            card_id: card.id,
                            inverted: this.dialogs.confirm_place_tunnel.inverted,
                        };
                        console.log(this.tag+" tile: ", this.mutableBoard[coords.y][coords.x]);
                        this.$forceUpdate();

                        // Update the player's hand
                        this.mutableHand.splice(this.dialogs.confirm_place_tunnel.card_index, 1);
                        if (response.data.new_card) this.mutableHand.push(response.data.new_card);

                        // Hide the dialog
                        this.dialogs.confirm_place_tunnel.loading = false;
                        this.dialogs.confirm_place_tunnel.show = false;

                    }.bind(this))

                    .catch(function(error) {
                        console.warn(this.tag+" failed to play tunnel card", error);
                        this.dialogs.confirm_place_tunnel.loading = false;
                        this.$toasted.show("Failed to play tunnel card, error: "+error, { duration: 3000 });
                    }.bind(this));

            },
            // Game board
            onClickedBoardTile(data) {
                console.log(this.tag+" clicked board tile: ", data);
                // If we're in gold location selection mode
                if (this.modes.select_gold_location.enabled) {

                    // Determine the ID of the start card
                    let startCardId = this.getCardIdByName("start");
                    if (startCardId) {
                        
                        // Determine the coordinates of the start card
                        let startCoordinates = null;
                        for (let y = 0; y < this.mutableBoard.length; y++) {
                            if (startCoordinates !== null) break;
                            for (let x = 0; x < this.mutableBoard[y].length; x++) {
                                if (this.mutableBoard[y][x] !== null && this.mutableBoard[y][x].card_id === startCardId) {
                                    startCoordinates = { x: x, y: y };
                                    break;
                                }
                            }
                        }

                        // Use the start card coordinates to determine the gold location card's coordinates
                        let goldLocationOne = { x: startCoordinates.x + 8, y: startCoordinates.y - 2 };
                        let goldLocationTwo = { x: startCoordinates.x + 8, y: startCoordinates.y };
                        let goldLocationThree = { x: startCoordinates.x + 8, y: startCoordinates.y + 2 };

                        // If the first gold location was selected
                        if (data.rowIndex === goldLocationOne.y && data.columnIndex === goldLocationOne.x) {
                            this.modes.select_gold_location.enabled = false;
                            this.dialogs.confirm_gold_location.gold_location = 1;
                            this.dialogs.confirm_gold_location.card_index = this.dialogs.view_card.index;
                            this.dialogs.confirm_gold_location.show = true;
                        }
                        // If the second gold location was selected
                        else if (data.rowIndex === goldLocationTwo.y && data.columnIndex === goldLocationTwo.x) {
                            this.modes.select_gold_location.enabled = false;
                            this.dialogs.confirm_gold_location.gold_location = 2;
                            this.dialogs.confirm_gold_location.card_index = this.dialogs.view_card.index;
                            this.dialogs.confirm_gold_location.show = true;
                        }
                        // If the third gold location was selected
                        else if (data.rowIndex === goldLocationThree.y && data.columnIndex === goldLocationThree.x) {
                            this.modes.select_gold_location.enabled = false;
                            this.dialogs.confirm_gold_location.gold_location = 3;
                            this.dialogs.confirm_gold_location.card_index = this.dialogs.view_card.index;
                            this.dialogs.confirm_gold_location.show = true;
                        }
                    }
                }
                // If we're in collapse tunnel mode
                else if (this.modes.select_tunnel.enabled) {
                    console.log(this.tag+" in collapse tunnel mode");

                    // If the clicked tile contains a card
                    if (this.mutableBoard[data.rowIndex][data.columnIndex] !== null) {

                        // Grab the card that's on the clicked tile
                        let cardOnTile = this.getCardById(this.mutableBoard[data.rowIndex][data.columnIndex].card_id);
                        if (cardOnTile) {

                            // If the card is a tunnel card
                            if (cardOnTile.type === "tunnel") {

                                this.modes.select_tunnel.enabled = false;
                                this.dialogs.confirm_collapse_tunnel.tunnel_coordinates = { x: data.columnIndex, y: data.rowIndex };
                                this.dialogs.confirm_collapse_tunnel.card_index = this.dialogs.view_card.index;
                                this.dialogs.confirm_collapse_tunnel.show = true;

                            } else {
                                this.$toasted.show("The card you selected is not a tunnel card", { duration: 3000 });
                            }

                        } else {
                            this.$toasted.show("Failed to retrieve the card on the clicked tile", { duration: 3000 });
                        }

                    } else {
                        this.$toasted.show("No tunnel card placed on the clicked tile", { duration: 3000 });
                    }

                }
                // If we're in place tunnel mode
                else if (this.modes.select_tile.enabled) {
                    console.log(this.tag+" in place tunnel mode");

                    // Determine what card is placed on the selected tile
                    if (this.mutableBoard[data.rowIndex][data.columnIndex] === null) {
                        console.log(this.tag+" tile is free");

                        // Make sure the tile is available
                        if (this.tileIsAvailable(data.rowIndex, data.columnIndex)) {
                            console.log(this.tag+" tile is available");

                            // Grab the card we want to place
                            let card = this.mutableHand[this.dialogs.view_card.index];
                            console.log(this.tag+" card we want to place: ", card);

                            // Make sure the card can be placed on the selected tile
                            if (this.cardCanBePlacedOnTile(data.rowIndex, data.columnIndex, card)) {
                                console.log(this.tag+" card can be placed on the given tile!");

                                this.modes.select_tile.enabled = false;
                                this.dialogs.confirm_place_tunnel.tunnel_coordinates = { x: data.columnIndex, y: data.rowIndex };
                                this.dialogs.confirm_place_tunnel.card_index = this.dialogs.view_card.index;
                                this.dialogs.confirm_place_tunnel.inverted = this.dialogs.view_card.inverted;
                                this.dialogs.confirm_place_tunnel.preview = this.generatePlaceTunnelPreview(data, card, this.dialogs.view_card.inverted);
                                this.dialogs.confirm_place_tunnel.show = true;

                            } else {
                                this.$toasted.show("Card does not fit on the specified tile", { duration: 3000 });
                            }

                        } else {
                            this.$toasted.show("Selected tile has no connecting tunnels around it", { duration: 3000 });
                        }

                    // If the card is unavailable
                    } else {
                        this.$toasted.show("Selected tile already has a card", { duration: 3000 });
                    }

                }
            },
            generatePlaceTunnelPreview(coordinates, card, inverted) {
                let out = [];
                // Determine the grid bounds
                let startRowIndex = coordinates.rowIndex - 1;
                let endRowIndex = coordinates.rowIndex + 1;
                let startColumnIndex = coordinates.columnIndex - 1;
                let endColumnIndex = coordinates.columnIndex + 1;
                // Generate the preview grid
                for (let ri = startRowIndex; ri <= endRowIndex; ri++) {
                    let row = [];
                    for (let ci = startColumnIndex; ci <= endColumnIndex; ci++) {
                        if (ci === coordinates.columnIndex && ri === coordinates.rowIndex) {
                            row.push({ card_id: card.id, inverted: inverted });
                        } else {
                            row.push(this.mutableBoard[ri][ci]);
                        }
                    }
                    out.push(row);
                }
                return out;
            },
            // General
            sendPerformActionRequest(action, data) {
                return new Promise(function(resolve, reject) {
                    // Compose the payload to send to the API
                    let payload = new FormData();
                    payload.append("game_id", this.game.id);
                    payload.append("action", action);
                    payload.append("data", JSON.stringify(data));
                    // Send API request
                    this.axios.post(this.performActionApiEndpoint, payload)
                        // Request succeeded
                        .then(function(response) {
                            // Operation succeeded
                            if (response.data.status === 'success') {
                                console.log(this.tag+" operation succeeded: ", response.data);
                                // Resolve promise
                                resolve(response.data);
                            }
                            // Operation failed
                            else {
                                console.warn(this.tag+" operation failed: ", response.data.error);
                                // Toast the error message
                                this.$toasted.show("API operation failed: "+response.data.error, { duration: 3000 });
                                // Reject promise
                                reject(response.data.error);
                            }
                        }.bind(this))
                        // Request failed
                        .catch(function(error) {
                            console.warn(this.tag+" request failed, error: ", error);
                            // Toast the error
                            this.$toasted.show("API request failed: "+error, { duration: 3000 });
                            // Reject promise
                            reject(error);
                        }.bind(this));
                }.bind(this));
            },
            getRoleLabelById(id) {
                for (let i = 0; i < this.roles.length; i++) {
                    if (this.roles[i].id === id) {
                        return this.roles[i].label;
                    }
                }
                return "Unknown role";
            },
            getCardById(id) {
                for (let i = 0; i < this.cards.length; i++) {
                    if (this.cards[i].id === id) {
                        return this.cards[i];
                    }
                }
                return false;
            },
            getCardIdByName(name) {
                for (let i = 0; i < this.cards.length; i++) {
                    if (this.cards[i].name === name) {
                        return this.cards[i].id;
                    }
                }
                return false;
            },
            getCardImageById(id) {
                let card = this.getCardById(id);
                if (card) {
                    return card.image_url;
                }
                return "";
            },
            tileIsAvailable(rowIndex, columnIndex) {
                console.log(this.tag+" checking if tile is available (row index: "+rowIndex+", colum index: "+columnIndex+")");

                // Check tile above
                let tileAbove = [rowIndex-1, columnIndex];
                console.log(this.tag+" tile above: ", tileAbove);
                if (this.mutableBoard[tileAbove[0]][tileAbove[1]] !== null) {
                    console.log(this.tag+" card id: ", this.mutableBoard[tileAbove[0]][tileAbove[1]].card_id);
                    let card = this.getCardById(this.mutableBoard[tileAbove[0]][tileAbove[1]].card_id);
                    console.log(this.tag+" card on tile: ", card);
                    if (card && card.type !== "gold_location" && (card.type === "start" || card.open_positions.includes("bottom"))) {
                        console.log(this.tag+" connecting card found on tile above, available");
                        return true;
                    }
                }

                // Check tile to the right
                let tileRight = [rowIndex, columnIndex+1];
                console.log(this.tag+" tile right: ", tileRight);
                if (this.mutableBoard[tileRight[0]][tileRight[1]] !== null) {
                    console.log(this.tag+" card id: ", this.mutableBoard[tileRight[0]][tileRight[1]].card_id);
                    let card = this.getCardById(this.mutableBoard[tileRight[0]][tileRight[1]].card_id);
                    console.log(this.tag+" card on tile: ", card);
                    if (card && card.type !== "gold_location" && (cart.type === "start" || card.open_positions.includes("left"))) {
                        console.log(this.tag+" connecting card found on tile to the right, available");
                        return true;
                    }
                }

                // Check tile below
                let tileBelow = [rowIndex+1, columnIndex];
                console.log(this.tag+" tile below: ", tileBelow);
                if (this.mutableBoard[tileBelow[0]][tileBelow[1]] !== null) {
                    console.log(this.tag+" card id: ", this.mutableBoard[tileBelow[0]][tileBelow[1]].card_id);
                    let card = this.getCardById(this.mutableBoard[tileBelow[0]][tileBelow[1]].card_id);
                    console.log(this.tag+" card on tile: ", card);
                    if (card && card.type !== "gold_location" && (card.type === "start" || card.open_positions.includes("top"))) {
                        console.log(this.tag+" connecting card found on tile below, available");
                        return true;
                    }
                }

                // Check tile to the left
                let tileLeft  = [rowIndex, columnIndex-1];
                console.log(this.tag+" tile left: ", tileLeft);
                if (this.mutableBoard[tileLeft[0]][tileLeft[1]] !== null) {
                    console.log(this.tag+" card id: ", this.mutableBoard[tileLeft[0]][tileLeft[1]].card_id);
                    let card = this.getCardById(this.mutableBoard[tileLeft[0]][tileLeft[1]].card_id);
                    console.log(this.tag+" card on tile: ", card);
                    if (card && card.type !== "gold_location" && (card.type === "start" || card.open_positions.includes("right"))) {
                        console.log(this.tag+" connecting card found on tile to the left, available");
                        return true;
                    }
                }

                // If we've reached this point the tile is available but has no connecting card
                console.log(this.tag+" tile has no connected tunnels, unavailable");
                return false;

            },
            cardCanBePlacedOnTile(rowIndex, columnIndex, card) {
                console.log(this.tag+" checking if card can be placed on tile: ", rowIndex, columnIndex, card);

                // Gather the required open positions based on the cards surrounding the selected coordinate
                let requiredOpenPositions = [];
                let requiredClosedPositions = [];

                // Check card above
                let coordsAbove = { rowIndex: rowIndex - 1, columnIndex: columnIndex };
                if (this.mutableBoard[coordsAbove.rowIndex][coordsAbove.columnIndex] !== null) {
                    console.log("tile above taken");
                    let card = this.getCardById(this.mutableBoard[coordsAbove.rowIndex][coordsAbove.columnIndex].card_id);
                    if (card) {
                        console.log("card found above", card);
                        if (card.type === "start") {
                            requiredOpenPositions.push("top");
                        } else {
                            if (card.open_positions.includes("bottom")) {
                                requiredOpenPositions.push("top");
                            } else {
                                requiredClosedPositions.push("top");
                            }
                        }
                    }
                }

                // Check card to the right
                let coordsRight = { rowIndex: rowIndex, columnIndex: columnIndex + 1 };
                if (this.mutableBoard[coordsRight.rowIndex][coordsRight.columnIndex] !== null) {
                    console.log("tile right taken");
                    let card = this.getCardById(this.mutableBoard[coordsRight.rowIndex][coordsRight.columnIndex].card_id);
                    if (card) {
                        console.log("card found right", card);
                        if (card.type === "start") {
                            requiredOpenPositions.push("right");
                        } else {
                            if (card.open_positions.includes("left")) {
                                requiredOpenPositions.push("right");
                            } else {
                                requiredClosedPositions.push("right");
                            }
                        }
                    }
                }

                // Check card below
                let coordsBelow = { rowIndex: rowIndex + 1, columnIndex: columnIndex };
                if (this.mutableBoard[coordsBelow.rowIndex][coordsBelow.columnIndex] !== null) {
                    console.log("tile below taken");
                    let card = this.getCardById(this.mutableBoard[coordsBelow.rowIndex][coordsBelow.columnIndex].card_id);
                    if (card) {
                        console.log("card found below", card);
                        if (card.type === "start") {
                            requiredOpenPositions.push("bottom");
                        } else {
                            if (card.open_positions.includes("top")) {
                                requiredOpenPositions.push("bottom");
                            } else {
                                requiredClosedPositions.push("bottom");
                            }
                        }
                    }
                }

                // Check card to the left
                let coordsLeft = { rowIndex: rowIndex, columnIndex: columnIndex - 1};
                if (this.mutableBoard[coordsLeft.rowIndex][coordsLeft.columnIndex] !== null) {
                    console.log("tile left taken", this.mutableBoard[coordsLeft.rowIndex][coordsLeft.columnIndex].card_id);
                    let card = this.getCardById(this.mutableBoard[coordsLeft.rowIndex][coordsLeft.columnIndex].card_id);
                    if (card) {
                        console.log("card found left", card);
                        if (card.type === "start") {
                            requiredOpenPositions.push("left");
                        } else {
                            if (card.open_positions.includes("right")) {
                                requiredOpenPositions.push("left");
                            } else {
                                requiredClosedPositions.push("left");
                            }
                        }                     
                    }
                }

                console.log(this.tag+" required open positions: ", requiredOpenPositions);
                console.log(this.tag+" required closed positions: ", requiredClosedPositions);

                // If the card meets the requirements (in it's current state)
                let meetsRequirements = true;
                if (!card.inverted) {
                    console.log(this.tag+" checking if (non-inverted) card fits on the tile");
                    // Validate against the required open & closed positions
                    for (let i = 0; i < requiredOpenPositions.length; i++) {
                        if (!card.open_positions.includes(requiredOpenPositions[i])) {
                            meetsRequirements = false;
                            break;
                        }
                    }
                    for (let i = 0; i < requiredClosedPositions.length; i++) {
                        if (card.open_positions.includes(requiredClosedPositions[i])) {
                            meetsRequirements = false;
                            break;
                        }
                    }
                } else {
                    console.log(this.tag+" checking if (inverted) card fits on the tile");
                    // Invert the card's open positions
                    let invertedOpenPositions = [];
                    for (let i = 0; i < card.open_positions.length; i++) {
                        if (card.open_positions[i] === "top") {
                            invertedOpenPositions.push("bottom");
                        } else if (card.open_positions[i] === "right") {
                            invertedOpenPositions.push("left");
                        } else if (card.open_positions[i] === "bottom") {
                            invertedOpenPositions.push("top");
                        } else if (card.open_positions[i] === "left") {
                            invertedOpenPositions.push("right");
                        }
                    }
                    // Validate against the required open & closed positions
                    for (let i = 0; i < requiredOpenPositions.length; i++) {
                        if (!invertedOpenPositions.includes(requiredOpenPositions[i])) {
                            meetsRequirements = false;
                            break;
                        }
                    }
                    for (let i = 0; i < requiredClosedPositions.length; i++) {
                        if (invertedOpenPositions.includes(requiredClosedPositions[i])) {
                            meetsRequirements = false;
                            break;
                        }
                    }
                }

                console.log(this.tag+" tile meets requirements: ", meetsRequirements);
                // Return result
                return meetsRequirements;

            },
            getPlayerById(id) {
                for (let i = 0; i < this.mutablePlayers.length; i++) {
                    if (this.mutablePlayers[i].id === id) {
                        return this.mutablePlayers[i];
                    }
                }
                return false;
            }
        },
        mounted() {
            this.initialize();
        }
    }
</script>

<style lang="scss">
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
            #role-selection {
                flex: 1;
                padding: 30px;
                box-sizing: border-box;
                #role-assigned {
                    width: 100%;
                    height: 100%;
                    display: flex;
                    align-items: center;
                    flex-direction: column;
                    justify-content: center;
                    .role-card {
                        margin: 15px 0 25px 0;
                    }
                }
                #role-not-assigned {
                    width: 100%;
                    height: 100%;
                    display: flex;
                    align-items: center;
                    flex-direction: column;
                    justify-content: center;
                }
                h1 {
                    text-align: center;
                }
                #role-selection__text {
                    margin: 0 0 30px 0;
                    text-align: center;
                }
                #available-roles {
                    margin: 0 0 30px 0;
                    h3 {
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
                                .role-name {}
                                .role-amount {
                                    margin: 0 0 0 5px;
                                }
                            }
                        }
                    }
                }
                #role-cards {
                    width: 100%;
                    h2 {
                        margin: 0 0 10px 0;
                        text-align: center;
                    }
                    h3 {
                        font-size: 1em;
                        text-align: center;
                        color: rgba(255, 255, 255, 0.75);
                    }
                    #role-cards__list {
                        display: flex;
                        flex-wrap: wrap;
                        flex-direction: row;
                        justify-content: center;
                        margin: 0 -15px -30px -15px;
                        .role-card__wrapper {
                            flex: 0 0 160px;
                            box-sizing: border-box;
                            padding: 0 15px 30px 15px;
                            
                        }
                    }
                }
                #role-card__selected {
                    #role-card__selected-content {
                        display: flex;
                        flex-direction: row;
                        justify-content: center;
                        #role-card__selected-card {
                            display: flex;
                            padding: 15px;
                            height: 200px;
                            color: #000;
                            flex: 0 0 130px;
                            margin: 0 30px 0 0;
                            border-radius: 3px;
                            position: relative;
                            align-items: center;
                            transition: all .3s;
                            box-sizing: border-box;
                            flex-direction: column;
                            justify-content: center;
                            background-color: rgba(255, 255, 255, 0.75);
                            #selected-card__title {
                                left: 0;
                                top: 15px;
                                width: 100%;
                                position: absolute;
                                text-align: center;
                            }
                            #selected-card__number {
                                font-size: 1.7em;
                                font-weight: 500;
                            }
                        }
                    }
                    #role-card__selected-text {
                        display: flex;
                        align-items: center;
                        flex-direction: column;
                        justify-content: center;
                        #selected-text__loading {
                            font-size: 2em;
                        }
                        #selected-text__title {
                            margin: 10px 0 0 0;
                        }
                    }
                }
            }
            #game-ui {
                height: 100%;
                display: flex;
                flex-direction: column;
                #board-area {
                    flex: 1;
                    position: relative;
                    #game-info {
                        top: 20px;
                        left: 25px;
                        position: absolute;
                        #game-info__current-round {
                            font-size: 2em;
                        }
                        #game-info__player-turn {
                            #my-turn {
                                font-weight: 500;
                                color: #ffd900;
                            }
                            #not-my-turn {
                                span {
                                    color: #ffd900;
                                    // text-decoration: underline;
                                }
                            }
                        }
                    }
                    #action-mode__wrapper {
                        left: 0;
                        top: 30px;
                        width: 100%;
                        display: flex;
                        position: absolute;
                        flex-direction: row;
                        justify-content: center;
                        #action-mode {
                            padding: 10px 15px;
                            border-radius: 3px;
                            box-sizing: border-box;
                            background-color: #333;
                        }
                    }
                    #game-board__wrapper {
                        width: 100%;
                        height: 100%;
                        position: relative;
                    }
                }
                #action-area {
                    display: flex;
                    padding: 30px;
                    flex: 0 0 250px;
                    flex-direction: row;
                    box-sizing: border-box;
                    background-color: hsl(0, 0%, 2%);
                    #my-role {
                        flex: 0 0 130px;
                        margin: 0 30px 0 0;
                        #my-role__title {
                            font-weight: 500;
                            font-size: 1.2em;
                            margin: 0 0 15px 0;
                            text-align: center;
                            text-transform: uppercase;
                        }
                        #my-role__card {
                            width: 130px;
                            height: 200px;
                            color: #000000;
                            border-radius: 3px;
                            position: relative;
                            background-color: hsl(0, 0%, 95%);
                            #my-role__card-text {
                                left: 0;
                                bottom: 0;
                                width: 100%;
                                padding: 15px 0;
                                text-align: center;
                                position: absolute;
                                box-sizing: border-box;
                            }
                        }
                    }
                    #my-hand {
                        flex: 1;
                        display: flex;
                        margin: 0 30px 0 0;
                        flex-direction: column;
                        justify-content: center;
                        #my-hand__title {
                            font-weight: 500;
                            font-size: 1.2em;
                            margin: 0 0 15px 0;
                            text-align: center;
                            text-transform: uppercase;
                        }
                        #my-hand__no-cards {
                            display: flex;
                            flex-direction: row;
                            align-items: center;
                            justify-content: center;
                        }
                        #my-hand__cards {
                            display: flex;
                            flex-direction: row;
                            justify-content: center;
                            .my-hand__card {
                                margin: 0 15px 0 0;
                                &:hover {
                                    cursor: pointer;
                                }
                                &:last-child {
                                    margin: 0;
                                }
                                &.selected {
                                    .my-hand__card-image {
                                        border: 2px solid #ffd900;
                                    }
                                }
                                .my-hand__card-image {
                                    width: 130px;
                                    height: 200px;
                                    border-radius: 3px;
                                    background-size: contain;
                                    background-repeat: no-repeat;
                                    background-position: center center;
                                }
                            }
                        }
                    }
                    #deck {
                        #deck__title {
                            font-weight: 500;
                            font-size: 1.2em;
                            margin: 0 0 15px 0;
                            text-align: center;
                            text-transform: uppercase;
                        }
                        #deck__card {
                            width: 130px;
                            height: 200px;
                            color: #000000;
                            border-radius: 3px;
                            position: relative;
                            background-color: hsl(0, 0%, 95%);
                            #deck__card-text {
                                width: 100%;
                                height: 100%;
                                display: flex;
                                font-size: 2em;
                                flex-direction: row;
                                align-items: center;
                                justify-content: center;
                            }
                        }
                    }
                    #my-actions {
                        flex: 0 0 300px;
                        #my-actions__title {
                            font-weight: 500;
                            font-size: 1.2em;
                            text-align: right;
                            margin: 0 0 15px 0;
                            text-transform: uppercase;
                        }
                        #my-actions__list {
                            width: 100%;
                            .action {
                                margin: 0 0 15px 0;
                                &:last-child {
                                    margin: 0;
                                }
                            }
                        }
                        #my-actions__too-many-cards {
                            text-align: right;
                        }
                        #my-actions__select-card {
                            text-align: right;
                        }
                        #my-actions__wait {
                            text-align: right;
                        }
                    }
                }
            }
        }
        #game-sidebar {
            display: flex;
            flex: 0 0 350px;
            flex-direction: column;
            background-color: hsl(0, 0%, 5%);
            #game-players__wrapper {
                flex: 1;
            }
            #game-chat__wrapper {
                flex: 0 0 300px;
            }
        }
    }
    .role-card {
        width: 130px;
        display: flex;
        padding: 15px;
        height: 200px;
        color: #000;
        border-radius: 3px;
        position: relative;
        align-items: center;
        transition: all .3s;
        box-sizing: border-box;
        flex-direction: column;
        justify-content: center;
        background-color: rgba(255, 255, 255, 0.75);
        &:hover {
            &.no-hover {
                cursor: default;
                background-color: rgba(255, 255, 255, 0.75);
            }
            cursor: pointer;
            background-color: rgba(255, 255, 255, 1);
            .role-card__select {
                opacity: 1;
            }
        }
        .role-card__title {
            left: 0;
            top: 15px;
            width: 100%;
            position: absolute;
            text-align: center;
        }
        .role-card__number {
            font-size: 1.7em;
            font-weight: 500;
        }
        .role-card__select {
            left: 0;
            opacity: 0;
            width: 100%;
            bottom: 15px;
            font-weight: 500;
            text-align: center;
            position: absolute;
            transition: all .3s;
        }
    }
    .card {
        width:130px;
        height: 200px;
        overflow: hidden;
        border-radius: 3px;
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center center;
        &.mb-15 {
            margin: 0 auto 15px auto;
        }
    }
    .card-info {
        display: flex;
        flex-direction: row;
        .card-info__card {
            height: 200px;
            flex: 0 0 130px;
            border-radius: 3px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center center;
            transition: all .3s;
            &.inverted {
                transform: rotate(180deg);
            }
        }
        .card-info__content {
            flex: 1;
            display: flex;
            margin: 0 0 0 30px;
            flex-direction: column;
            .card-info__description {
                flex: 1;
                .card-info__description-label {
                    font-size: .9em;
                    margin: 0 0 5px 0;
                    color: rgba(255, 255, 255, 0.45);
                }
                .card-info__description-text {

                }
            }
            .card-info__actions {
                .card-info__actions-text {

                }
                .card-info__actions-buttons {
                    .v-btn {
                        margin: 0 15px 0 0;
                        &:last-child {
                            margin: 0;
                        }
                    }
                }
            }
        }
    }
    .select-player {
        width: 100%;
        .select-player__title {
            margin: 0 0 10px 0;
        }
        .select-player__list {
            display: flex;
            flex-wrap: wrap;
            flex-direction: row;
            margin: 0 -15px -30px -15px;
            .select-player__list-item {
                flex: 0 0 50%;
                box-sizing: border-box;
                padding: 0 15px 30px 15px;
                .player-option {
                    padding: 15px;
                    color: #000;
                    border-radius: 3px;
                    transition: all .3s;
                    box-sizing: border-box;
                    background-color: rgba(255, 255, 255, .25);
                    &:hover {
                        cursor: pointer;
                        background-color: rgba(255, 255, 255, .5);
                    }
                    &.selected {
                        background-color: rgba(255, 255, 255, 1);
                        &:hover {
                            background-color: rgba(255, 255, 255, 1);
                        }
                    }
                }
            }
        }
    }
    #place-tunnel {
        display: flex;
        flex-direction: row;
        #place-tunnel__preview {
            margin: 0 30px 0 0;
            #preview {
                width: 195px;
                height: 300px;
                border: 1px dashed rgba(255, 255, 255, .1);
                .preview-row {
                    display: flex;
                    flex-direction: row;
                    border-bottom: 1px dashed rgba(255, 255, 255, .1);
                    &:last-child {
                        border-bottom: 0;
                    }
                    .preview-col {
                        height: 100px;
                        flex: 0 0 65px;
                        border-right: 1px dashed rgba(255, 255, 255, .1);
                        &:last-child {
                            border-right: 0;
                        }
                        .preview-card {
                            width: 65px;
                            height: 100px;
                            border-radius: 3px;
                            background-size: contain;
                            background-repeat: no-repeat;
                            background-position: center center;
                        }
                    }
                }
            }
        }
        #place-tunnel__text {
            flex: 1;
            display: flex;
            flex-direction: row;
            align-items: center;
        }
    }
    #reveal-gold-location {
        display: flex;
        flex-direction: column;
        justify-content: center;
        #reveal-gold-location__image {
            width: 150px;
            height: 150px;
            margin: 0 auto 15px auto;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center center;
        }
        #reveal-gold-location__text {
            text-align: center;
        }
    }
</style>