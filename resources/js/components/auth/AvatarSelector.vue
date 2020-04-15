<template>
    <div id="avatar-selector">

        <!-- Label -->
        <div id="avatar-selector__label">{{ label }}</div>

        <!-- Avatars -->
        <div id="avatar-selector__list">
            <div class="avatar-selector__list-item" v-for="(option, oi) in mutableAvatars" :key="oi">
                <div class="avatar" :style="{ backgroundImage: 'url('+option.url+')' }" :class="{ selected: option.selected }" @click="onClickAvatar(oi)"></div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        props: [
            "value",
            "label",
            "avatars",
            "baseUrl",
        ],
        data: () => ({
            tag: "[avatar-selector]",
            mutableAvatars: [],
        }),
        watch: {
            mutableAvatars: {
                deep: true,
                handler: function() {
                    for (let i = 0; i < this.mutableAvatars.length; i++) {
                        if (this.mutableAvatars[i].selected) {
                            this.$emit("input", this.mutableAvatars[i].value);
                            break;
                        }
                    }
                },
            },
        },
        methods: {
            initialize() {
                if (this.avatars !== undefined && this.avatars !== null && this.avatars.length > 0) {
                    for (let i = 0; i < this.avatars.length; i++) {
                        this.mutableAvatars.push({
                            selected: i === 0 && (this.value === undefined || this.value === null) ? true : false,
                            url: this.baseUrl + this.avatars[i],
                            value: this.avatars[i],
                        });
                    }
                }
                if (this.value !== undefined && this.value !== null && this.value !== "") {
                    for (let i = 0; i < this.mutableAvatars.length; i++) {
                        if (this.mutableAvatars[i].value === this.value) {
                            this.mutableAvatars[i].selected = true;
                            break;
                        }
                    }
                }
            },
            onClickAvatar(index) {
                if (this.mutableAvatars[index].selected) {
                    this.mutableAvatars[index].selected = false;
                } else {
                    for (let i = 0; i < this.mutableAvatars.length; i++) {
                        this.mutableAvatars[i].selected = false;
                    }
                    this.mutableAvatars[index].selected = true;
                }
            },
        },
        mounted() {
            this.initialize();
        }
    }
</script>

<style lang="scss">
    #avatar-selector {
        margin: 0 0 30px 0;
        #avatar-selector__label {
            font-size: .9em;
            margin: 0 0 5px 0;
            color: rgba(0, 0, 0, .65);
        }
        #avatar-selector__list {
            display: flex;
            flex-wrap: wrap;
            flex-direction: row;
            align-items: center;
            margin: 0 -5px -10px -5px;
            .avatar-selector__list-item {
                flex: 0 0 70px;
                box-sizing: border-box;
                padding: 0 5px 10px 5px;
                .avatar {
                    width: 70px;
                    height: 70px;
                    border-radius: 3px;
                    background-size: contain;
                    background-repeat: no-repeat;
                    background-position: center center;
                    background-color: hsl(0, 0%, 95%);
                    &.selected {
                        border: 4px solid #0cee00;
                    }
                    &:hover {
                        cursor: pointer;
                    }
                }
            }
        }
    }
</style>