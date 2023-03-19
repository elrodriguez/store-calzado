<template>
  <div>
    <input type="file" ref="input" @change="onChange">
    <img ref="image" :src="imageSrc" alt="Image" style="max-width: 100%; height: auto;">
    <button @click="cropImage">Hecho</button>
  </div>
</template>

<script>
import 'cropperjs/dist/cropper.css';
import Cropper from 'cropperjs';

export default {
  props: {
    imageUrl: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      imageSrc: ''
    };
  },
  methods: {
    onChange(event) {
      const files = event.target.files;
      if (files && files.length > 0) {
        const reader = new FileReader();
        reader.onload = () => {
          this.imageSrc = reader.result;
          this.initCropper();
        };
        reader.readAsDataURL(files[0]);
      }
    },
    initCropper() {
      const image = new Image();
      image.src = this.imageSrc;
      image.onload = () => {
        this.cropper = new Cropper(this.$refs.image, {
          aspectRatio: 16 / 9,
          viewMode: 2
        });
      };
    },
    cropImage() {
      const croppedCanvas = this.cropper.getCroppedCanvas();
      if (croppedCanvas) {
        const croppedImage = croppedCanvas.toDataURL();
        console.log(croppedImage);
      }
    }
  }
};
</script>
