<template>
    <div>
        Welcome {{ name }}
    </div>
</template>

<script>
export default {
    name: "Dashboard",
    data() {
        return {
            name: null,
        }
    },
    created() {
        if (window.openGDR.user) {
            this.name = window.openGDR.user.name
        }
    },
    beforeRouteEnter(to, from, next) {
        if (!window.openGDR.isLoggedin) {
            window.location.href = "/";
        }
        next();
    }
}
</script>
