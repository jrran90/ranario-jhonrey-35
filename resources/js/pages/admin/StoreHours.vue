<script setup lang="ts">
import {route} from 'ziggy-js';
import axios from "axios";

defineProps({
    storeHours: Array
});

const updateStoreHour = async (hour) => {
    const formattedHour = {
        ...hour,
        open_time: hour.open_time.length === 5 ? `${hour.open_time}:00` : hour.open_time,
        close_time: hour.close_time.length === 5 ? `${hour.close_time}:00` : hour.close_time,
    };

    try {
        const res = await axios.patch(route('admin.store-hours.update', hour.id), formattedHour)
        console.log(res.data.message)
        alert(res.data.message);
    } catch (error) {
        console.error(error)
    }
};

const getDayName = (dayOfWeek) => {
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    return days[dayOfWeek] || 'Unknown';
};
</script>

<template>
    <div class="p-6">
        <h2 class="text-2xl font-bold mb-4">Manage Store Hours</h2>

        <div v-for="hour in storeHours" :key="hour.id" class="mb-4 p-4 border rounded-lg">
            <p class="font-semibold">{{ getDayName(hour.day_of_week) }}</p>

            <div class="flex gap-4">
                <input
                    v-model="hour.open_time"
                    type="time"
                    class="border rounded p-2"
                />
                <input
                    v-model="hour.close_time"
                    type="time"
                    class="border rounded p-2"
                />
                <label class="flex items-center gap-2">
                    <input
                        v-model="hour.is_open"
                        type="checkbox"
                    />
                    Open
                </label>

                <button
                    @click="updateStoreHour(hour)"
                    class="bg-blue-500 text-white px-4 py-2 rounded"
                >
                    Save
                </button>
            </div>
        </div>
    </div>
</template>
