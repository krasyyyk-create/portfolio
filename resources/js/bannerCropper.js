import Cropper from 'cropperjs';

export default () => ({
    cropper: null,
    showModal: false,
    previewUrl: null,
    sourceUrl: null,
    hasCroppedFile: false,

    openPicker() {
        this.$refs.filePicker.click();
    },

    onFileSelect(event) {
        const file = event.target.files[0];
        if (!file) {
            return;
        }

        if (this.sourceUrl) {
            URL.revokeObjectURL(this.sourceUrl);
        }

        this.sourceUrl = URL.createObjectURL(file);
        this.showModal = true;

        this.$nextTick(() => {
            requestAnimationFrame(() => {
                const image = this.$refs.cropImage;

                if (this.cropper) {
                    this.cropper.destroy();
                    this.cropper = null;
                }

                image.src = this.sourceUrl;

                this.cropper = new Cropper(image, {
                    aspectRatio: 3,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 1,
                    responsive: true,
                    background: false,
                });
            });
        });
    },

    applyCrop() {
        if (!this.cropper) {
            return;
        }

        const canvas = this.cropper.getCroppedCanvas({
            width: 1200,
            height: 400,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        canvas.toBlob((blob) => {
            if (!blob) {
                return;
            }

            const file = new File([blob], 'banner.jpg', { type: 'image/jpeg' });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            this.$refs.bannerInput.files = dataTransfer.files;

            if (this.previewUrl) {
                URL.revokeObjectURL(this.previewUrl);
            }

            this.previewUrl = URL.createObjectURL(blob);
            this.hasCroppedFile = true;

            const removeCheckbox = this.$refs.removeBanner;
            if (removeCheckbox) {
                removeCheckbox.checked = false;
            }

            this.closeModal();
        }, 'image/jpeg', 0.92);
    },

    closeModal() {
        this.showModal = false;

        if (this.cropper) {
            this.cropper.destroy();
            this.cropper = null;
        }

        if (this.sourceUrl) {
            URL.revokeObjectURL(this.sourceUrl);
            this.sourceUrl = null;
        }

        if (this.$refs.filePicker) {
            this.$refs.filePicker.value = '';
        }
    },

    destroy() {
        this.closeModal();

        if (this.previewUrl) {
            URL.revokeObjectURL(this.previewUrl);
        }
    },
});
