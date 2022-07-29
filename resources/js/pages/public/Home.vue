<template>
    <div>
        Homepage
    </div>
</template>

<script>
export default {
    name: "Home",
    data() {
        return {
            //
        }
    },
    created() {
    },
    mounted() {
        if (window.openGDR.isLoggedin) {
            this.$router.push('/dashboard')
        }
    },
    methods: {},
}
</script>
