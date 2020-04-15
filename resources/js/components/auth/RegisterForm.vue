<template>
    <div id="register-form" class="elevation-1">

        <!-- Title -->
        <h1 id="register-form__title">{{ strings.title }}</h1>

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
                :error="hasErrors('email')"
                :error-messages="getErrors('email')">
            </v-text-field>
        </div>
        
        <!-- Password & confirm password -->
        <div class="form-fields">
            <div class="form-field">
                <v-text-field 
                    type="password" 
                    name="password"
                    :label="strings.password" 
                    v-model="form.password"
                    :error="hasErrors('password')"
                    :error-messages="getErrors('password')">
                </v-text-field>
            </div>
            <div class="form-field">
                <v-text-field 
                    type="password" 
                    name="password_confirmation" 
                    :label="strings.confirm_password" 
                    v-model="form.confirmPassword"
                    :error="hasErrors('password_confirmation')"
                    :error-messages="getErrors('password_confirmation')">
                </v-text-field>
            </div>
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

        <!-- Controls -->
        <div id="register-form__controls">
            <div id="register-form__controls-left">
                <!-- Link to login -->
                <a :href="loginHref">{{ strings.login }}</a>
            </div>
            <div id="register-form__controls-right">
                <!-- Submit button -->
                <v-btn type="submit" color="primary" depressed>
                    {{ strings.submit }}
                </v-btn>
            </div>
        </div>
        
    </div>
</template>

<script>
    export default {
        props: [
            "errors",
            "oldInput",
            "baseUrl",
            "avatars",
            "strings",
            "loginHref",
        ],
        data: () => ({
            tag: "[register-form]",
            annotationOptions: [],
            form: {
                username: "",
                name: "",
                email: "",
                password: "",
                confirmPassword: "",
            }
        }),
        methods: {
            initialize() {
                console.log(this.tag+" initialize");
                console.log(this.tag+" errors: ", this.errors);
                console.log(this.tag+" old input: ", this.oldInput);
                console.log(this.tag+" base url: ", this.baseUrl);
                console.log(this.tag+" avatars: ", this.avatars);
                console.log(this.tag+" title text: ", this.titleText);
                console.log(this.tag+" username text: ", this.usernameText);
                console.log(this.tag+" last name text: ", this.lastNameText);
                console.log(this.tag+" email text: ", this.emailText);
                console.log(this.tag+" password text: ", this.passwordText);
                console.log(this.tag+" confirm password text: ", this.confirmPasswordText);
                console.log(this.tag+" login text: ", this.loginText);
                console.log(this.tag+" login href: ", this.loginHref);
                this.initializeData();
            },
            initializeData() {
                if (this.oldInput !== undefined && this.oldInput !== null) {
                    if (this.oldInput.username !== null) this.form.username = this.oldInput.username;
                    if (this.oldInput.email !== null) this.form.email = this.oldInput.email;
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
    #register-form {
        width: 600px;
        padding: 25px;
        margin: 0 auto;
        border-radius: 3px;
        box-sizing: border-box;
        background-color: hsl(0, 0%, 100%);
        #register-form__controls {
            display: flex;
            margin: 15px 0 0 0;
            flex-direction: row;
            #register-form__controls-left, #register-form__controls-right {
                flex: 1;
                display: flex;
                flex-direction: row;
                align-items: center;
            }
            #register-form__controls-left {
                #login-link {
                    text-decoration: none;
                    color: hsl(0, 0%, 0%);
                }   
            }
            #register-form__controls-right {
                justify-content: flex-end;
                .v-btn {
                    margin: 0 0 0 15px;
                }
            }
        }
    }
    // Responsive
    @media (max-width: 650px) {
        #register-form {
            width: 100%;
        }
    }
    @media (max-width: 470px) {
        #register-form {
            .form-fields {
                flex-direction: column;
            }
        }
    }
    @media (max-width: 620px) {
        #register-form {
            #register-form__controls {
                flex-wrap: wrap;
                flex-direction: column-reverse;
                #register-form__controls-left, #register-form__controls-right {
                    flex: 0 0 100%;
                }
                #register-form__controls-left {
                    margin: 15px 0 0 0;
                }
                #register-form__controls-right {
                    justify-content: flex-start;
                    .v-btn {
                        margin: 0;
                    }
                }
            }
        }
    }
</style>
