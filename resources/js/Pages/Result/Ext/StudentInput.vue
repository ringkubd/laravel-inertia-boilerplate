<template>
<div>
    <input type="text" v-model="formData.student_id">
</div>
</template>

<script>
export default {
    name: "StudentInput",
    props: ['student_id'],
    data(){
        return {
            formData: {
                student_id: this.student_id
            }
        }
    }
}
</script>

<style scoped>

</style>
