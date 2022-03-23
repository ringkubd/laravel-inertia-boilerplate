<template>
<Authenticated>
<Layout1 name="Anwar" />
</Authenticated>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated";
import Layout1 from "@/Pages/FrontEnd/Backend/Layout/Layout1";

export default {
    name: "PageDesigner",
    components: {Layout1, Authenticated}
}
</script>

<style scoped>

</style>
