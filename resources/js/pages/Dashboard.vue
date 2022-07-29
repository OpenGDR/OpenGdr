<template>
    <div>
        Welcome
    </div>
</template>

<script>
export default {
    name: "Dashboard",
    data() {
        return {
        }
    },
    created() {
    },
    mounted() {
    },
    beforeRouteEnter(to, from, next) {
        if (!window.openGDR.isLoggedin) {
            this.$router.push('/')
        }
        next();
    }
}
</script>
