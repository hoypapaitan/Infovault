<template>
  <div 
    class="folder-card" 
    :class="{ 'active': active }"
    @click="$emit('click', year)"
  >
    <div class="folder-tab"></div>
    
    <div class="folder-content">
      <div class="folder-icon">
        <a-icon type="folder-open" theme="filled" />
      </div>
      <h1>{{ year }}</h1>
      <p>{{ count }} Graduates</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'YearFolderCard',
  props: {
    year: {
      type: [String, Number],
      required: true
    },
    count: {
      type: Number,
      default: 0
    },
    active: {
      type: Boolean,
      default: false
    }
  }
}
</script>

<style lang="scss" scoped>
.folder-card {
  position: relative;
  background-color: #d9f7be; /* Light Green */
  border-radius: 0 12px 12px 12px;
  padding: 20px;
  cursor: pointer;
  transition: all 0.3s ease;
  height: 160px;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  box-shadow: 0 4px 6px rgba(0,0,0,0.05);

  /* The Folder Tab */
  .folder-tab {
    position: absolute;
    top: -12px;
    left: 0;
    width: 70px;
    height: 12px;
    background-color: #d9f7be;
    border-radius: 8px 8px 0 0;
    transition: all 0.3s ease;
  }

  .folder-content {
    z-index: 1;
    .folder-icon {
      font-size: 32px;
      color: #52c41a;
      margin-bottom: 8px;
      opacity: 0.8;
      transition: color 0.3s ease;
    }

    h1 {
      font-size: 36px;
      font-weight: 800;
      color: #262626;
      margin: 0;
      line-height: 1.1;
      transition: color 0.3s ease;
    }
    
    p {
      font-size: 14px;
      color: #595959;
      margin: 0;
      font-weight: 600;
      transition: color 0.3s ease;
    }
  }

  /* Hover Effect */
  &:hover {
    transform: translateY(-5px);
    background-color: #b7eb8f; /* Medium Green */
    box-shadow: 0 10px 15px rgba(0,0,0,0.1);

    .folder-tab {
      background-color: #b7eb8f;
    }
  }

  /* Active/Selected State */
  &.active {
    background-color: #95de64; /* Strong Green */
    
    .folder-tab {
      background-color: #95de64;
    }

    .folder-content {
      h1, p, .folder-icon {
        color: #fff;
      }
    }
  }
}
</style>