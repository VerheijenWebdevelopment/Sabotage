<template>
    <div id="login-form">

        <!-- Title -->
        <h1 id="login-form__title">{{ titleText }}</h1>
        
        <!-- Email --> 
        <div class="form-field">
            <v-text-field 
                name="email" 
                v-model="form.email" 
                :label="emailText" 
                :error="hasErrors('email')" 
                :error-messages="getErrors('email')">
            </v-text-field>
        </div>
        
        <!-- Password -->
        <div class="form-field">
            <v-text-field 
                type="password" 
                name="password" 
                :label="passwordText"
                v-model="form.password" 
                :error="hasErrors('password')" 
                :error-messages="getErrors('password')">
            </v-text-field>
        </div>
        
        <!-- Controls -->
        <div class="form-controls">
            <div class="form-controls__left">
                <!-- Remember me -->
                <remember-me 
                    name="remember_me" 
                    v-model="form.rememberMe" 
                    :remember-me-text="rememberMeText">
                </remember-me>
            </div>
            <div class="form-controls__right">
                <!-- Submit -->
                <v-btn type="submit" color="primary">
                    {{ submitText}}
                </v-btn>
            </div>
        </div>

        <!-- Links -->
        <ul id="login-form__links">
            <a :href="forgotPasswordHref">{{ forgotPasswordText }}</a> - <a :href="registerHref">{{ registerText }}</a>
        </ul>

    </div>
</template>

<script>
    export default {
        props: [
            "oldInput",
            "errors",
            "prefillEmail",
            "titleText",
            "emailText",
            "passwordText",
            "rememberMeText",
            "submitText",
            "forgotPasswordText",
            "forgotPasswordHref",
            "registerText",
            "registerHref",
        ],
        data: () => ({
            tag: "[login-form]",
            form: {
                email: "",
                password: "",
                rememberMe: true,
            },
        }),
        methods: {
            initialize() {
                console.log(this.tag+" initializing");
                console.log(this.tag+" old input: ", this.oldInput);
                console.log(this.tag+" errors: ", this.errors);
                console.log(this.tag+" prefill email: ", this.prefillEmail);
                console.log(this.tag+" title text: ", this.titleText);
                console.log(this.tag+" email text: ", this.emailText);
                console.log(this.tag+" password text: ", this.passwordText);
                console.log(this.tag+" remember me text: ", this.rememberMeText);
                console.log(this.tag+" forgot password text: ", this.forgotPasswordText);
                console.log(this.tag+" forgot password href: ", this.forgetPasswordHref);
                console.log(this.tag+" register text: ", this.registerText);
                console.log(this.tag+" register href: ", this.registerHref);
                this.initializeData();
            },
            initializeData() {
                if (this.prefillEmail !== undefined && this.prefillEmail !== null && this.prefillEmail !== "") {
                    this.form.email = this.prefillEmail;
                }
                if (this.oldInput !== undefined && this.oldInput !== null) {
                    if (this.oldInput.email !== null) this.form.email = this.oldInput.email;
                    if (this.oldInput.rememberMe !== null) this.form.rememberMe = this.oldInput.rememberMe ===  "true" ? true : false;
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
        },
    }
</script>

<style lang="scss">
    #login-form {
        #login-form__links {
            text-align: center;
        }
    }
</style>
