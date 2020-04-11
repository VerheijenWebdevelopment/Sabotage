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
                        Waiting for other players to select their role.
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

                <!-- Title -->
                <h1 id="rewards-ui__title">Round has ended!</h1>
                
                <!-- Subtitle -->
                <h2 id="rewards-ui__subtitle">{{ rewards.winning_team }} have won</h2>

                <!-- Instructions (saboteurs won) -->
                <div id="rewards-ui__instructions" v-if="rewards.winning_team === 'saboteurs'">
                    All saboteurs have been awarded {{ rewards.saboteur_reward }} gold
                </div>

                <!-- Instructions (golddiggers won) -->
                <div id="rewards-ui__instructions" v-if="rewards.winning_team === 'golddiggers' && mutablePlayerRole.name === 'digger' && !myTurn && !allRevealedPlayersHaveChosenReward">
                    {{ playerAtPlay.user.username }} is choosing their reward
                </div>
                <div id="rewards-ui__instructions" v-if="rewards.winning_team === 'golddiggers' && mutablePlayerRole.name !== 'digger'">
                    Golddiggers are choosing their rewards
                </div>

                <!-- Players -->
                <div id="rewards-ui__players" v-if="showRewardPlayers">
                    <div class="player-wrapper" v-for="(revealedPlayer, rpi) in rewards.revealed_players" :key="rpi">
                        <div class="player" :class="{ 'winner': revealedPlayer.winner }">
                            <div class="player-username">{{ getUsernameByPlayerId(revealedPlayer.player.id) }}</div>
                            <div class="player-role__wrapper">
                                <div class="player-role">{{ revealedPlayer.role.label }}</div>
                            </div>
                            <div class="player-reward">
                                <span class="gold">{{ revealedPlayer.gold_awarded }} gold</span> awarded
                            </div>
                            <div class="player-ready">
                                <span class="ready" v-if="revealedPlayer.ready">Ready!</span>
                                <span class="not-ready" v-if="!revealedPlayer.ready">Not ready</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Golddigger reward selection -->
                <div id="rewards-ui__reward-cards__wrapper" v-if="showRewardCards">
                    <!-- Title -->
                    <div id="rewards-ui__reward-cards__title">Select your reward</div>
                    <!-- Loading -->
                    <div id="rewards-ui__loading" v-if="rewards.loading">
                        Loading
                    </div>
                    <!-- Reward cards -->
                    <div id="rewards-ui__reward-cards" v-if="rewards.winning_team === 'golddiggers' && !rewards.loading && currentPlayerRevealed">
                        <div class="reward-card__wrapper" v-for="(n, ni) in mutableGame.num_cards_reward_deck" :key="ni">
                            <div class="reward-card" @click="onClickRewardCard(ni)">
                                <div class="reward-card__title">Reward Card</div>
                                <div class="reward-card__number">#{{ ni + 1 }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reward card -->
                <div id="rewards-ui__reward-card" v-if="currentPlayerRevealed.gold_reward_card !== null">
                    <div id="reward-card__title">{{ currentPlayerRevealed.gold_reward_card.text }}</div>
                    <div id="reward-card__subtitle">{{ currentPlayerRevealed.gold_reward_card.description }}</div>
                </div>

                <!-- Ready up for next round -->
                <div id="rewards-ui__ready-up" v-if="showRewardReadyUp">
                    <!-- Waiting on other player -->
                    <div id="ready-up__text" v-if="currentPlayerRevealed.ready">
                        Waiting for other players to be ready
                    </div>
                    <!-- Ready button -->
                    <div id="ready-up__button" v-if="!currentPlayerRevealed.ready">
                        <v-btn color="success" dark @click="onClickReadyForNextRound" :loading="rewards.loading">
                            Ready for next round!
                        </v-btn>
                    </div>
                </div>

            </div>

            <!-- Game over UI -->
            <div id="game-over-ui" v-if="mutableGame !== null && phase === 'endgame'">

                <!-- Title -->
                <h1 id="game-over-ui__title">Game over!</h1>

                <!-- Winners -->
                <div id="winners">
                    <div id="winners-title">Winners</div>
                    <div id="winners-list" v-if="winners !== null">
                        <div class="winner-wrapper" v-for="(winner, wi) in gameWinners" :key="wi">
                            <div class="winner">
                                <div class="winner-name">
                                    {{ winner.user.username }}
                                </div>
                                <div class="winner-score">
                                    {{ winner.score }} gold
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
            <div class="dialog" v-if="viewCardDialogCard">
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
                                        <!-- Invert (tunnel) card -->
                                        <span class="tooltip-wrapper" v-if="viewCardDialogCard.type === 'tunnel'">
                                            <v-btn class="icon-only" @click="onClickInvertCard">
                                                <i class="fas fa-sync-alt"></i>
                                            </v-btn>
                                        </span>
                                        <!-- Play card -->
                                        <v-btn @click="onClickPlayCard" v-if="showPlayCardButton">
                                            Play card
                                        </v-btn>
                                        <!-- Can't play card -->
                                        <span class="tooltip-wrapper" v-if="!showPlayCardButton">
                                            <v-tooltip bottom>
                                                <template v-slot:activator="{ on }">
                                                    <div v-on="on">
                                                        <v-btn disabled dark>Play card</v-btn>
                                                    </div>
                                                </template>
                                                <span>{{ missingToolsText }}</span>
                                            </v-tooltip>
                                        </span>
                                        <!-- Fold card -->
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
            <div class="dialog" v-if="dialogs.fold_card.index !== null && foldCardDialogCard !== undefined">
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
                    <div class="dialog-title">{{ sabotageToolDialogTitle }}</div>
                    <!-- Select player -->
                    <div class="select-player">
                        <div class="select-player__title">Select target player</div>
                        <div class="select-player__list">
                            <div class="select-player__list-item" v-for="(player, pi) in mutablePlayersExcludingMe" :key="pi">
                                <div class="player-option" :class="{ selected: dialogs.confirm_sabotage_player.player_id === player.id }" @click="onClickSelectPlayer('sabotage', player.id)">
                                    {{ player.user.username }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Select tool -->
                    <div class="select-tool" v-if="showSabotageToolSelection(dialogs.confirm_sabotage_player.card_index)">
                        <div class="select-tool__title">Select tool to sabotage</div>
                        <div class="select-tool__list">
                            <div class="select-tool__list-item" v-for="(tool, ti) in getSabotageToolSelectionOptions(dialogs.confirm_sabotage_player.card_index)" :key="ti">
                                <div class="tool-option" :class="{ selected: dialogs.confirm_sabotage_player.tool === tool }" @click="onClickSelectTool('sabotage', tool)">
                                    {{ tool }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Controls -->
                <div class="dialog-controls">
                    <div class="dialog-controls__left">
                        <v-btn text dark @click="onClickCancelSabotagePlayer">
                            Cancel
                        </v-btn>
                    </div>
                    <div class="dialog-controls__right">
                        <v-btn dark @click="onClickConfirmSabotagePlayer" :loading="dialogs.confirm_sabotage_player.loading" :disabled="disableConfirmSabotageTool">
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
                    <div class="dialog-title">{{ recoverToolDialogTitle }}</div>
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
                    <div class="select-tool" v-if="showRecoverToolSelection(dialogs.confirm_recover_player.card_index)">
                        <div class="select-tool__title">Select tool to recover</div>
                        <div class="select-tool__list">
                            <div class="select-tool__list-item" v-for="(tool, ti) in getRecoverToolSelectionOptions(dialogs.confirm_recover_player.card_index)" :key="ti">
                                <div class="tool-option" :class="{ selected: dialogs.confirm_recover_player.tool === tool }" @click="onClickSelectTool('recover', tool)">
                                    {{ tool }}
                                </div>
                            </div>
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
                        <v-btn dark @click="onClickConfirmRecoverPlayer" :loading="dialogs.confirm_recover_player.loading" :disabled="disableConfirmRecoverTool">
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
                                        <div class="preview-card" v-if="col !== null" :class="{ inverted: col.inverted }" :style="{ backgroundImage: 'url('+getCardImageById(col.card_id)+')' }"></div>
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
                    card_index: null,
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
            },
            rewards: {
                loading: false,
                revealed_players: [],
                saboteur_reward: 0,
                winning_team: "",
            },
            winners: null,
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
                if (this.mutableHand.length > 0 && this.dialogs.view_card.index !== null) {
                    return this.mutableHand[this.dialogs.view_card.index];
                }
                return false;
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
            mutablePlayersExcludingMe() {
                let out = [];
                for (let i = 0; i < this.mutablePlayers.length; i++) {
                    if (this.mutablePlayers[i].id !== this.mutablePlayer.id) {
                        out.push(this.mutablePlayers[i]);
                    }
                }
                return out;
            },
            disableConfirmRecoverTool() {
                let card = this.mutableHand[this.dialogs.confirm_recover_player.card_index];
                if (card) {
                    if (card.name === "recover_pickaxe_light" || card.name === "recover_pickaxe_cart" || card.name === "recover_light_cart") {
                        return this.dialogs.confirm_recover_player.player_id === null || this.dialogs.confirm_recover_player.tool === null ? true : false;
                    } else {
                        return this.dialogs.confirm_recover_player.player_id === null ? true : false;
                    }
                }
                return true;
            },
            disableConfirmSabotageTool() {
                let card = this.mutableHand[this.dialogs.confirm_sabotage_player.card_index];
                if (card) {
                    if (card.name === "sabotage_pickaxe_light" || card.name === "sabotage_pickaxe_cart" || card.name === "sabotage_light_cart") {
                        return this.dialogs.confirm_sabotage_player.player_id === null || this.dialogs.confirm_sabotage_player.tool === null ? true : false;
                    } else {
                        return this.dialogs.confirm_sabotage_player.player_id === null ? true : false;
                    }
                }
                return true;
            },
            allToolsAvailable() {
                return this.mutablePlayer.pickaxe_available && this.mutablePlayer.light_available && this.mutablePlayer.cart_available;
            },
            missingToolsText() {
                let missing = [];
                if (!this.mutablePlayer.pickaxe_available) missing.push("pickaxe");
                if (!this.mutablePlayer.light_available) missing.push("light");
                if (!this.mutablePlayer.cart_available) missing.push("cart");
                if (missing.length === 0) {
                    return "All of your tools are available";
                } else if (missing.length === 1) {
                    return "Your "+missing[0]+" is disabled";
                } else {
                    let out = "Your ";
                    if (missing.length === 2) {
                        out += missing[0]+" and "+missing[1]+" are disabled";
                    } else {
                        out += missing[0]+", "+missing[1]+" and "+missing[2]+" are disabled";
                    }
                    return out;
                }
            },
            showPlayCardButton() {
                if (this.viewCardDialogCard.type === "tunnel") {
                    console.log("tunnel!", this.allToolsAvailable);
                    return this.allToolsAvailable;
                }
                return true;
            },
            recoverToolDialogTitle() {
                if (this.dialogs.confirm_recover_player.card_index !== null) {
                    let out = "Recover player's ";
                    let card = this.mutableHand[this.dialogs.confirm_recover_player.card_index];
                    if (card.name === "recover_pickaxe") {
                        out += "pickaxe";
                    } else if (card.name === "recover_light") {
                        out += "light";
                    } else if (card.name === "recover_cart") {
                        out += "cart";
                    } else {
                        out += "tool";
                    }
                    return out;
                }
                return "";
            },
            sabotageToolDialogTitle() {
                if (this.dialogs.confirm_sabotage_player.card_index !== null) {
                    let out = "Sabotage player's ";
                    let card = this.mutableHand[this.dialogs.confirm_sabotage_player.card_index];
                    if (card.name === "sabotage_pickaxe") {
                        out += "pickaxe";
                    } else if (card.name === "sabotage_light") {
                        out += "light";
                    } else if (card.name === "sabotage_cart") {
                        out += "cart";
                    } else {
                        out += "tool";
                    }
                    return out;
                }
                return "";
            },
            currentPlayerIsReadyForNextRound() {
                if (this.mutableGame.revealed_players !== undefined && this.mutableGame.revealed_players !== null && this.mutableGame.revealed_players.length > 0) {
                    for (let i = 0; i < this.mutableGame.revealed_players.length; i++) {
                        if (this.mutableGame.revealed_players[i].player.id === this.mutablePlayer.id) {
                            return this.mutableGame.revealed_players[i].ready;
                        }
                    }
                }
                return false;
            },
            currentPlayerHasSelectedReward() {
                for (let i = 0; i < this.rewards.revealed_players.length; i++) {
                    if (this.rewards.revealed_players[i].player.id === this.mutablePlayer.id) {
                        return this.rewards.revealed_players[i].selected_reward;
                    }
                }
                return false;
            },
            showRewardPlayers() {
                // If the saboteurs won; show the players at all times (since no further actions are required)
                if (this.rewards.winning_team === 'saboteurs') {
                    return true;
                // If the gold diggers won;
                } else {
                    // And the player is a digger; show the players once the player has selected their reward
                    if (this.mutablePlayerRole.name === 'digger') {
                        return this.currentPlayerHasSelectedReward;
                    // If the player is a saboteur; show the players at all times
                    } else {
                        return true;
                    }
                }
            },
            showRewardCards() {
                return this.rewards.winning_team === 'golddiggers' && this.mutablePlayerRole.name === 'digger' && !this.currentPlayerHasSelectedReward && this.myTurn;
            },
            showRewardReadyUp() {
                if (this.rewards.winning_team === 'saboteurs') {
                    return true;
                } else {
                    if (this.mutablePlayerRole.name === 'digger') {
                        return this.currentPlayerHasSelectedReward;
                    } else {
                        return true;
                    }
                }
            },
            currentPlayerRevealed() {
                if (this.rewards.revealed_players.length > 0) {
                    for (let i = 0; i < this.rewards.revealed_players.length; i++) {
                        if (this.rewards.revealed_players[i].player.id === this.mutablePlayer.id) {
                            return this.rewards.revealed_players[i];
                        }
                    }
                }
                return false;
            },
            allRevealedPlayersHaveChosenReward() {
                let players_found = 0;
                let players_rewarded = 0;
                for (let i = 0; i < this.rewards.revealed_players.length; i++) {
                    if (this.rewards.revealed_players[i].winner) {
                        players_found += 1;
                        if (this.rewards.revealed_players[i].selected_reward) {
                            players_rewarded += 1;
                        }
                    }
                }
                return players_found == players_rewarded;
            },
            gameWinners() {
                let out = [];
                for (let i = 0; i < this.winners.length; i++) {
                    for (let j = 0; j < this.mutablePlayers.length; j++) {
                        if (this.winners[i] === this.mutablePlayers[j].id) {
                            out.push(this.mutablePlayers[j]);
                            break;
                        }
                    }
                }
                return out;
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

                this.rewards.revealed_players = this.game.revealed_players;
                this.rewards.saboteur_reward = this.game.saboteur_reward;
                this.rewards.winning_team = this.game.winning_team;

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
                    .listen('Game\\GoldLocationRevealed', this.onGoldLocationRevealed)
                    .listen('Game\\NewRoundStarted', this.onNewRoundStarted)
                    .listen('Game\\PlayerIsReadyForNextRound', this.onPlayerReadyForNextRound)
                    .listen('Game\\PlayerWasAwardedGold', this.onPlayerWasAwardedGold)
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
                
                // Grab the player and target player
                let player = this.getPlayerById(e.player.id);
                let targetPlayer = this.getPlayerById(e.target_player.id);

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

                // If we were the target player, update our mutablePlayer as well
                if (targetPlayer.id === this.mutablePlayer.id) {
                    if (e.tool === "pickaxe") {
                        this.mutablePlayer.pickaxe_available = 0;
                    } else if (e.tool === "light") {
                        this.mutablePlayer.light_available = 0;
                    } else if (e.tool === "cart") {
                        this.mutablePlayer.cart_available = 0;
                    }
                }

                // Notify the player what just happened
                this.$toasted.show(player.user.username+" blocked "+targetPlayer.user.username+"'s "+e.tool, { duration: 5000 });

            },
            onPlayerToolRecovered(e) {
                console.log(this.tag+"[event] player tool recovered", e);

                // Grab the player and target player
                let player = this.getPlayerById(e.player.id);
                let targetPlayer = this.getPlayerById(e.target_player.id);

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

                // If we were the target player, update our mutablePlayer as well
                if (targetPlayer.id === this.mutablePlayer.id) {
                    if (e.tool === "pickaxe") {
                        this.mutablePlayer.pickaxe_available = 1;
                    } else if (e.tool === "light") {
                        this.mutablePlayer.light_available = 1;
                    } else if (e.tool === "cart") {
                        this.mutablePlayer.cart_available = 1;
                    }
                }

                // Notify the player what just happened
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
                this.mutableBoard = e.game.board;
                // Notify user wassup
                let player = this.getPlayerById(e.player.id);
                this.$toasted.show(player.user.username+" has placed a tunnel on "+e.coordinates.x+":"+e.coordinates.y, { duration: 3000 });
            },
            onPlayerCollapsedTunnel(e) {
                console.log(this.tag+"[event] player collapsed tunnel", e);
                // Update the board
                this.mutableBoard = e.game.board
                // Notify user wassup
                let player = this.getPlayerById(e.player.id);
                this.$toasted.show(player.user.username+" has collapsed the tunnel on "+e.coordinates.x+":"+e.coordinates.y, { duration: 3000 });
            },
            onGoldLocationRevealed(e) {
                console.log(this.tag+"[event] gold location revealed: ", e);
                // Update the board
                this.mutableBoard = e.game.board;
                // Notify user wassup
                this.$toasted.show("Gold location #"+e.gold_location+" has been revealed!", { duration: 3000 });
            },
            onNewRoundStarted(e) {
                console.log(this.tag+"[event] new round started", e);

                // this.mutableGame.turn = 1;
                // this.mutableGame.num_cards_in_deck = e.game.num_cards_in_deck;
                // this.mutableBoard = e.game.board;
                // this.mutablePlayerRole = null;
                // this.round = e.game.round;
                // this.phase = "role_selection";

                location.reload(); 

            },
            onPlayerReadyForNextRound(e) {
                console.log(this.tag+"[event] player ready for next round", e);

                // Update the player's ready status
                for (let i = 0; i < this.rewards.revealed_players.length; i++) {
                    if (this.rewards.revealed_players[i].player.id == e.player.id) {
                        this.rewards.revealed_players[i].ready = true;
                        break;
                    }
                }

            },
            onPlayerWasAwardedGold(e) {
                console.log(this.tag+"[event] player was awarded gold", e);

                // Update available cards
                this.mutableGame.num_cards_reward_deck = e.game.num_cards_reward_deck;

                // Update the revealed player entry for the player who was awarded gold
                if (this.rewards.revealed_players.length > 0) {
                    for (let i = 0; i < this.rewards.revealed_players.length; i++) {
                        if (this.rewards.revealed_players[i].player.id === e.player.id) {
                            this.rewards.revealed_players[i].gold_awarded = e.gold;
                            this.rewards.revealed_players[i].selected_reward = true;
                            this.rewards.revealed_players[i].gold_reward_card = e.reward_card;
                            break;
                        }
                    }
                }

                // Update player's score
                for (let i = 0; i < this.mutablePlayers.length; i++) {
                    if (this.mutablePlayers[i].id === e.player.id) {
                        this.mutablePlayers[i].score += e.gold;
                        break;
                    }
                }

            },
            onTurnEnded(e) {
                console.log(this.tag+"[event] turn ended", e);

                // Set the number of the player who's turn it is currently
                this.playersTurn = e.game.player_turn;
                this.mutableGame.num_cards_in_deck = e.game.num_cards_in_deck;
                
            },
            onRoundEnded(e) {
                console.log(this.tag+"[event] round ended", e);

                setTimeout(function() {

                    // Set the appropriate game phase
                    this.phase = "rewards";

                    // Set the player who is at turn
                    this.playersTurn = e.game.player_turn;
    
                    // Save the updated mutable game
                    this.rewards.revealed_players = e.data.revealed_players;
                    this.rewards.saboteur_reward = e.data.saboteur_reward;
                    this.rewards.winning_team = e.data.winning_team;

                    this.mutableGame.num_cards_reward_deck = e.data.num_cards_reward_deck;
                    
                }.bind(this), 2000);

            },
            onGameEnded(e) {
                console.log(this.tag+"[event] game ended", e);
                
                // Set the appropriate game phase
                this.phase = "endgame";

                this.winners = e.winners;

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
                this.dialogs.view_card.inverted = false;
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

                        // Reset this dialog
                        this.dialogs.confirm_gold_location.gold_location = null;

                    }.bind(this))

                    // When request fails
                    .catch(function(error) {
                        console.warn(this.tag+" failed to play enlighten card", error);
                        this.dialogs.confirm_gold_location.loading = false;
                    }.bind(this));

            },
            // Confirm sabotage & recover dialog
            onClickSelectPlayer(dialog, playerId) {
                console.log(this.tag+" clicked player option", dialog, playerId);
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
                console.log(this.tag+" clicked tool option", dialog, tool);
                if (dialog === "recover") {
                    if (this.dialogs.confirm_recover_player.tool === tool) {
                        this.dialogs.confirm_recover_player.tool = null;
                    } else {
                        this.dialogs.confirm_recover_player.tool = tool;
                    }
                } else {
                    if (this.dialogs.confirm_sabotage_player.tool === tool) {
                        this.dialogs.confirm_sabotage_player.tool = null;
                    } else {
                        this.dialogs.confirm_sabotage_player.tool = tool;
                    }
                }
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
                        
                        // Reset the dialog
                        this.dialogs.confirm_sabotage_player.player_id = null;
                        this.dialogs.confirm_sabotage_player.tool = null;

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
                console.log(this.tag+" tool: ", tool);

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
                        
                        // Reset the dialog
                        this.dialogs.confirm_sabotage_player.player_id = null;
                        this.dialogs.confirm_sabotage_player.tool = null;

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

                // Init loading
                this.dialogs.confirm_collapse_tunnel.loading = true;

                // Compose data to send with API request
                let data = {
                    index: this.dialogs.confirm_collapse_tunnel.card_index,
                    target_coordinates: this.dialogs.confirm_collapse_tunnel.tunnel_coordinates
                };

                // Send API request
                this.sendPerformActionRequest("play_card", data)

                    // If request succeeded
                    .then(function(response) {

                        // Update the game board
                        this.mutableBoard = response.data.board;

                        // Update the player's hand
                        this.mutableHand.splice(this.dialogs.confirm_collapse_tunnel.card_index, 1);
                        if (response.data.new_card) this.mutableHand.push(response.data.new_card);

                        // Hide the dialog
                        this.dialogs.confirm_collapse_tunnel.loading = false;
                        this.dialogs.confirm_collapse_tunnel.show = false;

                        // Reset this dialog
                        this.dialogs.confirm_collapse_tunnel.tunnel_coordinates = null;

                    }.bind(this))

                    // If request failed
                    .catch(function(error) {
                        console.warn(this.tag+" failed to play collapse tunnel card", error);
                        this.dialogs.confirm_collapse_tunnel.loading = false;
                        this.$toasted.show("Failed to play collapse tunnel card, error: "+error);
                    }.bind(this));

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
                        console.log(this.tag+" operation succeeded!", response);
                        
                        // Grab card from player's hand
                        let card = this.mutableHand[this.dialogs.confirm_place_tunnel.card_index];
                        console.log(this.tag+" card: ", card);

                        // Update the game board
                        this.mutableBoard = response.data.board;

                        // Update the player's hand
                        this.mutableHand.splice(this.dialogs.confirm_place_tunnel.card_index, 1);
                        if (response.data.new_card) this.mutableHand.push(response.data.new_card);

                        // Hide the dialog
                        this.dialogs.confirm_place_tunnel.loading = false;
                        this.dialogs.confirm_place_tunnel.show = false;

                        // Reset this dialog
                        this.dialogs.confirm_place_tunnel.tunnel_coordinates = null;

                    }.bind(this))

                    .catch(function(error) {
                        console.warn(this.tag+" failed to play tunnel card", error);
                        this.dialogs.confirm_place_tunnel.loading = false;
                        this.$toasted.show("Failed to play tunnel card, error: "+error, { duration: 3000 });
                    }.bind(this));

            },
            // Rewards phase
            onClickReadyForNextRound() {
                console.log(this.tag+" clicked ready for next round button");

                // Start loading
                this.rewards.loading = true;

                // Send API request
                this.sendPerformActionRequest("flag_ready")

                    .then(function(response) {
                        console.log(this.tag+" succesfully flagged player as ready", response);

                        // Stop loading
                        this.rewards.loading = false;

                        // Update the player's ready state
                        for (let i = 0; i < this.rewards.revealed_players.length; i++) {
                            if (this.rewards.revealed_players[i].player.id === this.mutablePlayer.id) {
                                this.rewards.revealed_players[i].ready = true;
                                break;
                            }
                        }

                    }.bind(this))

                    .catch(function(error) {
                        console.warn(this.tag+" failed to flag player as ready, error: ", error);
                        this.rewards.loading = false;
                    }.bind(this));

            },
            onClickRewardCard(index) {
                console.log(this.tag+" clicked reward card, index: ", index);

                this.rewards.loading = true;

                let data = { index: index };

                this.sendPerformActionRequest("select_gold_reward_card", data)

                    .then(function(response) {
                        console.log(this.tag+" request succeeded", response);
                        
                        this.rewards.loading = false;

                        for (let i = 0; i < this.rewards.revealed_players.length; i++) {
                            if (this.rewards.revealed_players[i].player.id === this.mutablePlayer.id) {
                                this.rewards.revealed_players[i].gold_reward_card = response.data.reward_card;
                                this.rewards.revealed_players[i].gold_awarded = response.data.reward;
                                this.rewards.revealed_players[i].selected_reward = true;
                                break;
                            }
                        }

                    }.bind(this))

                    .catch(function(error) {
                        console.warn(this.tag+" failed to send select reward card request", error);

                        this.rewards.loading = false;

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
                        if (this.tileHasConnectingCards(data.rowIndex, data.columnIndex)) {
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
                            if (this.mutableBoard[ri] !== undefined && this.mutableBoard[ri][ci] !== undefined) {
                                row.push(this.mutableBoard[ri][ci]);
                            } else {
                                row.push(null);
                            }
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
            tileHasCard(rowIndex, columnIndex) {
                // console.log("TILE HAS CARD ", rowIndex, columnIndex);
                if (this.mutableBoard[rowIndex] !== undefined && this.mutableBoard[rowIndex][columnIndex] !== undefined && this.mutableBoard[rowIndex][columnIndex] !== null) {
                    return true;
                }
                return false;
            },
            tileHasConnectingCards(rowIndex, columnIndex) {
                // console.log(this.tag+" checking if tile is available (row index: "+rowIndex+", colum index: "+columnIndex+")");
                
                // Check tile above
                let tileAbove = [rowIndex-1, columnIndex];
                let tileRight = [rowIndex, columnIndex+1];
                let tileBelow = [rowIndex+1, columnIndex];
                let tileLeft  = [rowIndex, columnIndex-1];

                // Connected tiles we find
                let connectedCards = [];

                // Count the number of connected gold location tiles
                let connectedGoldLocations = 0;

                // console.log(this.tag+" tile above: ", tileAbove);
                if (this.tileHasCard(tileAbove[0], tileAbove[1])) {
                    let card = this.getCardById(this.mutableBoard[tileAbove[0]][tileAbove[1]].card_id);
                    let inverted = this.mutableBoard[tileAbove[0]][tileAbove[1]].inverted;
                    if (card && (card.type === "gold_location" || card.type === "start" || (!inverted && card.open_positions.includes("bottom")) || (inverted && card.open_positions.includes("top")))) {
                        connectedCards.push(card);
                        if (card.type === "gold_location") connectedGoldLocations += 1;
                    }
                }

                // Check tile to the right
                if (this.tileHasCard(tileRight[0], tileRight[1])) {
                    let card = this.getCardById(this.mutableBoard[tileRight[0]][tileRight[1]].card_id);
                    let inverted = this.mutableBoard[tileRight[0]][tileRight[1]].inverted;
                    if (card && (card.type === "gold_location" || card.type === "start" || (!inverted && card.open_positions.includes("left")) || (inverted && card.open_positions.includes("right")))) {
                        connectedCards.push(card);
                        if (card.type === "gold_location") connectedGoldLocations += 1;
                    }
                }

                // Check tile below
                if (this.tileHasCard(tileBelow[0], tileBelow[1])) {
                    let card = this.getCardById(this.mutableBoard[tileBelow[0]][tileBelow[1]].card_id);
                    let inverted = this.mutableBoard[tileBelow[0]][tileBelow[1]].inverted;
                    if (card && (card.type === "gold_location" || card.type === "start" || (!inverted && card.open_positions.includes("top")) || (inverted && card.open_positions.includes("bottom")))) {
                        connectedCards.push(card);
                        if (card.type === "gold_location") connectedGoldLocations += 1;
                    }
                }

                // Check tile to the left
                if (this.tileHasCard(tileLeft[0], tileLeft[1])) {
                    let card = this.getCardById(this.mutableBoard[tileLeft[0]][tileLeft[1]].card_id);
                    let inverted = this.mutableBoard[tileLeft[0]][tileLeft[1]].inverted;
                    if (card && (card.type === "gold_location" || card.type === "start" || (!inverted && card.open_positions.includes("right")) || (inverted && card.open_positions.includes("left")))) {
                        connectedCards.push(card);
                        if (card.type === "gold_location") connectedGoldLocations += 1;
                    }
                }

                // If we've found compatible connected cards
                if (connectedCards.length > 0)
                {
                    // Make sure we're not connected to only gold locations; since that would allow illegal moves
                    if (connectedCards.length === connectedGoldLocations)
                    {
                        console.log(this.tag+" only gold locations connected");
                        return false;
                    }

                    // Otherwise all is good
                    return true;
                }

                // If we've reached this point the tile is available but has no connecting card
                return false;

            },
            cardCanBePlacedOnTile(rowIndex, columnIndex, card) {
                // console.log(this.tag+" checking if card can be placed on tile: ", rowIndex, columnIndex, card);

                // Gather the required open positions based on the cards surrounding the selected coordinate
                let requiredOpenPositions = [];
                let requiredClosedPositions = [];

                // Check card above
                let coordsAbove = { rowIndex: rowIndex - 1, columnIndex: columnIndex };
                // console.log("checking tile above: ", coordsAbove);
                if (this.tileHasCard(coordsAbove.rowIndex, coordsAbove.columnIndex)) {
                    // console.log(this.tag+" tile above taken");
                    let card = this.getCardById(this.mutableBoard[coordsAbove.rowIndex][coordsAbove.columnIndex].card_id);
                    if (card) {
                        // console.log(this.tag+" card found above: ", card);
                        if (card.type === "start" || card.type === "gold_location") {
                            requiredOpenPositions.push("top");
                        } else {
                            if (this.mutableBoard[coordsAbove.rowIndex][coordsAbove.columnIndex].inverted) {
                                if (card.open_positions.includes("top")) {
                                    requiredOpenPositions.push("top");
                                } else {
                                    requiredClosedPositions.push("top");
                                }
                            } else {
                                if (card.open_positions.includes("bottom")) {
                                    requiredOpenPositions.push("top");
                                } else {
                                    requiredClosedPositions.push("top");
                                }
                            }
                        }
                    }
                }

                // Check card to the right
                let coordsRight = { rowIndex: rowIndex, columnIndex: columnIndex + 1 };
                // console.log("checking tile to right", coordsRight);
                if (this.tileHasCard(coordsRight.rowIndex, coordsRight.columnIndex)) {
                    // console.log("tile right taken");
                    let card = this.getCardById(this.mutableBoard[coordsRight.rowIndex][coordsRight.columnIndex].card_id);
                    if (card) {
                        // console.log("card found right", card);
                        if (card.type === "start" || card.type === "gold_location") {
                            requiredOpenPositions.push("right");
                        } else {
                            if (this.mutableBoard[coordsRight.rowIndex][coordsRight.columnIndex].inverted) {
                                if (card.open_positions.includes("right")) {
                                    requiredOpenPositions.push("right");
                                } else {
                                    requiredClosedPositions.push("right");
                                }
                            } else {
                                if (card.open_positions.includes("left")) {
                                    requiredOpenPositions.push("right");
                                } else {
                                    requiredClosedPositions.push("right");
                                }
                            }
                        }
                    }
                }

                // Check card below
                let coordsBelow = { rowIndex: rowIndex + 1, columnIndex: columnIndex };
                // console.log("checking tile below", coordsBelow);
                if (this.tileHasCard(coordsBelow.rowIndex, coordsBelow.columnIndex)) {
                    // console.log("tile below taken");
                    let card = this.getCardById(this.mutableBoard[coordsBelow.rowIndex][coordsBelow.columnIndex].card_id);
                    if (card) {
                        // console.log("card found below", card);
                        if (card.type === "start" || card.type === "gold_location") {
                            requiredOpenPositions.push("bottom");
                        } else {
                            if (this.mutableBoard[coordsBelow.rowIndex][coordsBelow.columnIndex].inverted) {
                                if (card.open_positions.includes("bottom")) {
                                    requiredOpenPositions.push("bottom");
                                } else {
                                    requiredClosedPositions.push("bottom");
                                }
                            } else {
                                if (card.open_positions.includes("top")) {
                                    requiredOpenPositions.push("bottom");
                                } else {
                                    requiredClosedPositions.push("bottom");
                                }
                            }
                        }
                    }
                }

                // Check card to the left
                let coordsLeft = { rowIndex: rowIndex, columnIndex: columnIndex - 1};
                // console.log("checking tile to left", coordsLeft, this.mutableBoard[coordsLeft.rowIndex][coordsLeft.columnIndex], this.tileHasCard(coordsLeft.rowIndex, coordsLeft.columIndex));
                if (this.tileHasCard(coordsLeft.rowIndex, coordsLeft.columnIndex)) {
                    // console.log("tile left taken");
                    let card = this.getCardById(this.mutableBoard[coordsLeft.rowIndex][coordsLeft.columnIndex].card_id);
                    if (card) {
                        // console.log("card found left", card);
                        if (card.type === "start" || card.type === "gold_location") {
                            requiredOpenPositions.push("left");
                        } else {
                            if (this.mutableBoard[coordsLeft.rowIndex][coordsLeft.columnIndex].inverted) {
                                if (card.open_positions.includes("left")) {
                                    requiredOpenPositions.push("left");
                                } else {
                                    requiredClosedPositions.push("left");
                                }
                            } else {
                                if (card.open_positions.includes("right")) {
                                    requiredOpenPositions.push("left");
                                } else {
                                    requiredClosedPositions.push("left");
                                }
                            }
                        }                     
                    }
                }

                // console.log(this.tag+" required open positions: ", requiredOpenPositions);
                // console.log(this.tag+" required closed positions: ", requiredClosedPositions);

                // If the card meets the requirements (in it's current state)
                let meetsRequirements = true;
                if (!this.dialogs.view_card.inverted) {
                    // console.log(this.tag+" checking if (non-inverted) card fits on the tile");
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
                    // console.log(this.tag+" checking if (inverted) card fits on the tile");
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

                // console.log(this.tag+" tile meets requirements: ", meetsRequirements);
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
            },
            showRecoverToolSelection(cardIndex) {
                let recoverCard = this.mutableHand[cardIndex];
                if (recoverCard) {
                    return recoverCard.name === "recover_pickaxe_light" || recoverCard.name === "recover_pickaxe_axe" || recoverCard.name === "recover_light_cart";
                }
                return false;
            },
            getRecoverToolSelectionOptions(cardIndex) {
                let out = [];
                let recoverCard = this.mutableHand[cardIndex];
                if (recoverCard) {
                    if (recoverCard.name === "recover_pickaxe_light") {
                        out.push("pickaxe");
                        out.push("light");
                    } else if (recoverCard.name === "recover_pickaxe_cart") {
                        out.push("pickaxe");
                        out.push("cart");
                    } else {
                        out.push("light");
                        out.push("cart");
                    }
                }
                return out;
            },
            showSabotageToolSelection(cardIndex) {
                let sabotageCard = this.mutableHand[this.dialogs.confirm_sabotage_player.card_index];
                if (sabotageCard) {
                    return sabotageCard.name === "sabotage_pickaxe_light" || sabotageCard.name === "sabotage_pickaxe_axe" || sabotageCard.name === "sabotage_light_cart";
                }
                return false;
            },
            getSabotageToolSelectionOptions(cardIndex) {
                let out = [];
                let sabotageCard = this.mutableHand[this.dialogs.confirm_sabotage_player.card_index];
                if (sabotageCard) {
                    if (sabotageCard.name === "sabotage_pickaxe_light") {
                        out.push("pickaxe");
                        out.push("light");
                    } else if (sabotageCard.name === "sabotage_pickaxe_cart") {
                        out.push("pickaxe");
                        out.push("cart");
                    } else {
                        out.push("light");
                        out.push("cart");
                    }
                }
                return out;
            },
            getUsernameByPlayerId(playerId) {
                for (let i = 0; i < this.mutablePlayers.length; i++) {
                    if (this.mutablePlayers[i].id === playerId) {
                        return this.mutablePlayers[i].user.username;
                    }
                }
                return "-";
            },
        },
        mounted() {
            this.initialize();
        },
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
                        z-index: 10;
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
                        z-index: 10;
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
            #rewards-ui {
                width: 100%;
                height: 100%;
                display: flex;
                padding: 100px;
                align-items: center;
                box-sizing: border-box;
                flex-direction: column;
                justify-content: center;
                #rewards-ui__title {
                    text-align: center;
                }
                #rewards-ui__subtitle {
                    font-size: .9em;
                    text-align: center;
                    text-transform: uppercase;
                }
                #rewards-ui__instructions {
                    margin: 0 auto;
                    color: #000000;
                    padding: 8px 15px;
                    border-radius: 3px;
                    margin: 0 0 40px 0;
                    box-sizing: border-box;
                    background-color: rgba(255, 255, 255, 0.75);
                }
                #rewards-ui__reward-cards__wrapper {
                    #rewards-ui__reward-cards__title {
                        font-size: 1.7em;
                        font-weight: 500;
                        margin: 0 0 20px 0;
                        text-align: center;
                    }
                    #rewards-ui__loading {
                        text-align: center;
                    }
                    #rewards-ui__reward-cards {
                        display: flex;
                        flex-wrap: wrap;
                        flex-direction: row;
                        justify-content: center;
                        margin: 0 -15px -30px -15px;
                        .reward-card__wrapper {
                            flex: 0 0 130px;
                            box-sizing: border-box;
                            padding: 0 15px 30px 15px;
                            .reward-card {
                                width: 130px;
                                height: 200px;
                                display: flex;
                                color: #000000;
                                border-radius: 3px;
                                transition: all .3s;
                                align-items: center;
                                flex-direction: column;
                                justify-content: center;
                                background-color: rgba(255, 255, 255, 0.8);
                                &:hover {
                                    cursor: pointer;
                                    background-color: #ffffff;
                                }
                                .reward-card__title {
                                    
                                }
                                .reward-card__number {
                                    font-size: 1.5em;
                                }
                            }
                        }
                    }
                }
                #rewards-ui__reward-card {
                    margin: 50px 0 0 0;
                    #reward-card__title {
                        font-size: 1.3em;
                        text-align: center;
                    }
                    #reward-card__subtitle {
                        text-align: center;
                    }
                }
                #rewards-ui__players {
                    width: 100%;
                    display: flex;
                    flex-wrap: wrap;
                    flex-direction: row;
                    justify-content: center;
                    margin: 50px -15px -30px -15px;
                    .player-wrapper {
                        flex: 0 0 250px;
                        box-sizing: border-box;
                        padding: 0 15px 30px 15px;
                        .player {
                            width: 100%;
                            padding: 20px;
                            color: #000000;
                            border-radius: 3px;
                            box-sizing: border-box;
                            background-color: #ffffff;
                            &.winner {
                                margin: -5px;
                                border: 5px solid #00a100;
                            }
                            .player-username {
                                text-align: center;
                                font-size: 1.2em;
                            }
                            .player-avatar {
                                width: 100px;
                                height: 100px;
                                margin: 15px auto;
                                border-radius: 50px;
                                background-color: hsl(0, 0%, 95%);
                            }
                            .player-role__wrapper {
                                margin: 2px 0;
                                display: flex;
                                flex-direction: row;
                                align-items: center;
                                justify-content: center;
                                .player-role {
                                    color: #fff;
                                    font-size: .7em;
                                    padding: 2px 5px;
                                    text-align: center;
                                    border-radius: 3px;
                                    display: inline-block;
                                    box-sizing: border-box;
                                    background-color: #333;
                                    text-transform: uppercase;
                                }
                            }
                            .player-reward {
                                margin: 20px 0;
                                text-align: center;
                                .gold {
                                    color: #db9600;
                                    font-weight: 500;
                                }
                            }
                            .player-ready {
                                text-align: center;
                                .ready {
                                    color: #39b410;
                                }
                                .not-ready {
                                    color: #b20404;
                                }
                            }
                        }
                    }
                }
                #rewards-ui__ready-up {
                    margin: 50px 0 0 0;
                    #ready-up__text {
                        text-align: center;
                        margin: 0 0 15px 0;
                    }
                    #ready-up__action {
                        text-align: center;
                    }
                }
            }
            #game-over-ui {
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                flex-direction: column;
                justify-content: center;
                #winners {
                    #winners-title {
                        font-size: 1.5em;
                        font-weight: 500;
                        text-align: center;
                        margin: 50px 0 10px 0;
                        text-transform: uppercase;
                    }
                    #winners-list {
                        display: flex;
                        flex-direction: row;
                        align-items: center;
                        justify-content: center;
                        margin: 0 -15px -30px -15px;
                        .winner-wrapper {
                            flex: 0 0 200px;
                            box-sizing: border-box;
                            padding: 0 15px 30px 15px;
                            .winner {
                                width: 250px;
                                height: 100px;
                                padding: 25px;
                                display: flex;
                                color: #000000;
                                border-radius: 3px;
                                align-items: center;
                                flex-direction: column;
                                box-sizing: border-box;
                                justify-content: center;
                                background-color: #fff;
                                .winner-title {
                                    font-size: 1.1em;
                                    font-weight: 500;
                                    text-align: center;
                                }   
                                .winner-score {
                                    text-align: center;
                                    color: #ffd900;
                                }
                            }
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
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                    .v-btn {
                        margin: 0 15px 0 0;
                        &:last-child {
                            margin: 0;
                        }
                    }
                    .tooltip-wrapper {
                        padding: 0 15px 0 0;
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
    .select-tool {
        width: 100%;
        margin: 15px 0 0 0;
        .select-tool__title {
            margin: 0 0 10px 0;
        }
        .select-tool__list {
            display: flex;
            flex-wrap: wrap;
            flex-direction: row;
            margin: 0 -15px -30px -15px;
            .select-tool__list-item {
                flex: 0 0 50%;
                box-sizing: border-box;
                padding: 0 15px 30px 15px;
                .tool-option {
                    padding: 15px;
                    color: #000;
                    border-radius: 3px;
                    transition: all .3s;
                    box-sizing: border-box;
                    text-transform: capitalize;
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
                        overflow: hidden;
                        position: relative;
                        border-right: 1px dashed rgba(255, 255, 255, .1);
                        &:last-child {
                            border-right: 0;
                        }
                        .preview-card {
                            top: 0;
                            left: 0;
                            width: 65px;
                            height: 100px;
                            border-radius: 3px;
                            position: absolute;
                            background-size: contain;
                            background-repeat: no-repeat;
                            background-position: center center;
                            &.inverted {
                                transform: rotate(180deg);
                            }
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