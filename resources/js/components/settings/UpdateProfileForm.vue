<template>
    <div id="update-profile-form" class="elevation-1">

        <!-- Username -->
        <div class="form-field">
            <v-text-field
                name="username"
                :label="strings.username"
                v-model="form.username"
                :errors="hasErrors('username')"
                :error-messages="getErrors('username')">
            </v-text-field>
        </div>

        <!-- Email -->
        <div class="form-field">
            <v-text-field
                name="email"
                :label="strings.email"
                v-model="form.email"
                :errors="hasErrors('email')"
                :error-messages="getErrors('email')">
            </v-text-field>
        </div>
        
        <!-- Avatar -->
        <div class="form-field">
            <avatar-selector
                :label="strings.avatar"
                :avatars="avatars"
                :base-url="baseUrl"
                v-model="form.avatar_url">
            </avatar-selector>
            <input type="hidden" name="avatar" :value="form.avatar_url">
        </div>


        <!-- Form controls -->
        <div class="form-controls">
            <div class="form-controls__left">
                <v-btn text :href="cancelHref">
                    <i class="fas fa-arrow-left"></i>
                    {{ strings.cancel }}
                </v-btn>    
            </div>
            <div class="form-controls__right">
                <v-btn depressed color="primary" type="submit">
                    <i class="fas fa-save"></i>
                    {{ strings.submit }}
                </v-btn>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        props: [
            "user",
            "errors",
            "oldInput",
            "avatars",
            "strings",
            "baseUrl",
            "cancelHref",
        ],
        data: () => ({
            tag: "[update-profile-form]",
            form: {
                name: "",
                email: "",
                username: "",
                avatar: "",
            }
        }),
        methods: {
            initialize() {
                console.log(this.tag+" initializing");
                console.log(this.tag+" user: ", this.user);
                console.log(this.tag+" errors: ", this.errors);
                console.log(this.tag+" old input: ", this.oldInput);
                console.log(this.tag+" strings: ", this.strings);
                console.log(this.tag+" cancel href: ", this.cancelHref);
                this.initializeData();
            },
            initializeData() {
                if (this.user !== undefined && this.user !== null) {
                    this.form.email = this.user.email;
                    this.form.username = this.user.username;
                    this.form.avatar = this.user.avatar_url;
                }
                if (this.oldInput !== undefined && this.oldInput !== null) {
                    if (this.oldInput.email !== null) this.form.email = this.oldInput.email;
                    if (this.oldInput.username !== null) this.form.username = this.oldInput.username;
                    if (this.oldInput.avatar !== null) this.form.avatar = this.oldInput.avatar;
                }
            },
            hasErrors(field) {
                if (this.errors !== undefined && this.errors !== null && this.errors.length > 0) {
                    if (this.errors[field] !==  undefined &&  this.errors[field] !== "") {
                        return true;
                    }
                }
                return false;
            },
            getErrors(field) {
                if (this.errors !== undefined && this.errors !== null && this.errors[field] !== undefined && this.errors[field] !== null) {
                    return this.errors[field];
                }
                return [];
            },
        },
        mounted() {
            this.initialize();
        }
    }
</script>

<style lang="scss">
    #update-profile-form {
        padding: 25px;
        border-radius: 3px;
        box-sizing: border-box;
        background-color: #ffffff;
        .image-field__image {
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center center;
            background-color: hsl(0, 0%, 95%);
        }
    }
</style>