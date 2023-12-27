<template>
  <div>
    <p>{{ currentTime }}</p>
  </div>
</template>

<script>
export default {
  data() {
    return {
      currentTime: this.getCurrentTime(),
    };
  },
  mounted() {
    // Refresh the time every minute
    this.timer = setInterval(() => {
      this.currentTime = this.getCurrentTime();
    }, 60000); // 60000 milliseconds = 1 minute
  },
  beforeDestroy() {
    // Clear the timer when the component is destroyed to prevent memory leaks
    clearInterval(this.timer);
  },
  methods: {
    getCurrentTime() {
      const now = new Date();
      const hours = now.getHours().toString().padStart(2, '0');
      const minutes = now.getMinutes().toString().padStart(2, '0');
      const seconds = now.getSeconds().toString().padStart(2, '0');
      return `${hours}:${minutes}:${seconds}`;
    },
  },
};
</script>
