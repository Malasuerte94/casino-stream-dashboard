<template>
    <AppLayout title="Report">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <ReportUser></ReportUser>
                    <ReportBonusHunt></ReportBonusHunt>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import ReportUser from '../Components/MainComponents/ReportUser.vue';
import ReportBonusHunt from '../Components/MainComponents/ReportBonusHunt.vue';
export default {
    data() {
        return {
            stream: [],
            settings: [],
        };
    },
    components: { AppLayout, ReportUser, ReportBonusHunt }
}
</script>
