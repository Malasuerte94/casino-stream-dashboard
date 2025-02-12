<template>
  <div ref="sceneContainer" class="scene-container"></div>
</template>

<script>
import { markRaw } from 'vue';
import * as THREE from 'three';
import { TextGeometry } from 'three/examples/jsm/geometries/TextGeometry.js';
import { FontLoader } from 'three/examples/jsm/loaders/FontLoader.js';

export default {
  props: ['allViewers'],
  data() {
    return {
      scene: markRaw(new THREE.Scene()),
      camera: markRaw(new THREE.PerspectiveCamera(45, 1, 0.1, 1000)),
      renderer: null,
      viewerGroup: markRaw(new THREE.Group()),
      particlesGroup: markRaw(new THREE.Group()),
      viewerMeshes: {},
      animationFrameId: null,
      // Title orbiting group and parameters.
      titleGroup: null,
      titleOrbitAngle: 0,
      titleOrbitRadius: 25,
      titleMesh: null // Will store the 3D text mesh.
    };
  },
  mounted() {
    this.initScene();
    this.animate = this.animate.bind(this);
    this.animate();
    window.addEventListener("resize", this.onWindowResize);

    // Simulate 200 users for testing:
    // const simulatedUsers = this.simulateUsers();
    // simulatedUsers.forEach((viewer, index) => {
    //   this.addViewer(viewer, index, simulatedUsers.length);
    // });
  },
  beforeUnmount() {
    if (this.animationFrameId) cancelAnimationFrame(this.animationFrameId);
    window.removeEventListener("resize", this.onWindowResize);
  },
  watch: {
    allViewers: {
      handler(newViewers) {
        newViewers.forEach((viewer, index) => {
          if (!this.viewerMeshes[viewer.id]) {
            this.addViewer(viewer, index, newViewers.length);
          } else {
            this.updateViewer(viewer, index, newViewers.length);
          }
        });
      },
      deep: true,
      immediate: true
    }
  },
  methods: {
    initScene() {
      const container = this.$refs.sceneContainer;
      const width = container.clientWidth;
      const height = container.clientHeight;
      this.camera.aspect = width / height;
      this.camera.updateProjectionMatrix();
      this.camera.position.z = 70;
      this.renderer = new THREE.WebGLRenderer({antialias: true, alpha: true});
      this.renderer.setSize(width, height);
      container.appendChild(this.renderer.domElement);
      this.scene.add(this.viewerGroup);
      this.scene.add(this.particlesGroup);
      const ambientLight = new THREE.AmbientLight(0xffffff, 0.8);
      this.scene.add(ambientLight);
      this.renderer.setClearColor(0x000000, 0);
      this.createParticles();
      //this.addTitleAndCurve();
    },
    onWindowResize() {
      const container = this.$refs.sceneContainer;
      const width = container.clientWidth;
      const height = container.clientHeight;
      this.camera.aspect = width / height;
      this.camera.updateProjectionMatrix();
      this.renderer.setSize(width, height);
    },
    getViewerPosition(index, total, radius = 20) {
      const phi = Math.acos(1 - (2 * (index + 1) / (total + 1)));
      const theta = Math.PI * (1 + Math.sqrt(5)) * (index + 1);
      return new THREE.Vector3(
          radius * Math.sin(phi) * Math.cos(theta),
          radius * Math.sin(phi) * Math.sin(theta),
          radius * Math.cos(phi)
      );
    },
    roundRect(ctx, x, y, width, height, radius) {
      ctx.beginPath();
      ctx.moveTo(x + radius, y);
      ctx.lineTo(x + width - radius, y);
      ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
      ctx.lineTo(x + width, y + height - radius);
      ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
      ctx.lineTo(x + radius, y + height);
      ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
      ctx.lineTo(x, y + radius);
      ctx.quadraticCurveTo(x, y, x + radius, y);
      ctx.closePath();
      ctx.fill();
    },
    addViewer(viewer, index, total) {
      const canvas = document.createElement('canvas');
      const context = canvas.getContext('2d');
      const fontSize = 12;
      const font = `Bold ${fontSize}px Arial`;
      context.font = font;
      const textWidth = context.measureText(viewer.user).width;
      const paddingX = 5, paddingY = 5;
      canvas.width = textWidth + paddingX * 2;
      canvas.height = fontSize + paddingY * 2;

      let backgroundColor = "#000f22";
      if (viewer.eliminated) backgroundColor = "#700800";
      else if (viewer.picked) backgroundColor = "#9f7400";

      context.fillStyle = backgroundColor;
      this.roundRect(context, 0, 0, canvas.width, canvas.height, 10);
      context.font = font;
      context.textBaseline = 'middle';
      context.fillStyle = '#ffffff';
      context.fillText(viewer.user, paddingX, canvas.height / 2);

      const texture = new THREE.CanvasTexture(canvas);
      const material = new THREE.SpriteMaterial({map: texture, transparent: true});
      const sprite = new THREE.Sprite(material);

      sprite.userData.canvas = canvas;
      sprite.userData.font = font;
      sprite.userData.paddingX = paddingX;
      sprite.userData.paddingY = paddingY;
      sprite.userData.text = viewer.user;

      const scaleFactor = 0.1;
      const finalScale = new THREE.Vector3(canvas.width * scaleFactor, canvas.height * scaleFactor, 1);
      sprite.scale.set(0, 0, 1);

      sprite.position.copy(this.getViewerPosition(index, total));
      this.viewerGroup.add(sprite);
      this.viewerMeshes[viewer.id] = sprite;

      let currentScale = 0;
      const animatePopup = () => {
        currentScale += 0.05;
        if (currentScale < 1) {
          sprite.scale.set(finalScale.x * currentScale, finalScale.y * currentScale, 1);
          requestAnimationFrame(animatePopup);
        } else {
          sprite.scale.set(finalScale.x, finalScale.y, 1);
        }
      };
      animatePopup();
    },
    updateViewer(viewer, index, total) {
      const sprite = this.viewerMeshes[viewer.id];
      if (!sprite) return;
      const canvas = sprite.userData.canvas;
      const context = canvas.getContext('2d');
      const font = sprite.userData.font;
      const paddingX = sprite.userData.paddingX;
      const paddingY = sprite.userData.paddingY;

      context.clearRect(0, 0, canvas.width, canvas.height);
      let backgroundColor = "#000f22";
      if (viewer.eliminated) backgroundColor = "#700800";
      else if (viewer.picked) backgroundColor = "#9f7400";

      context.fillStyle = backgroundColor;
      this.roundRect(context, 0, 0, canvas.width, canvas.height, 10);
      context.font = font;
      context.textBaseline = 'middle';
      context.fillStyle = '#ffffff';
      context.fillText(viewer.user, paddingX, canvas.height / 2);
      sprite.material.map.needsUpdate = true;
    },
    createParticles() {
      const particleCount = 300;
      const positions = new Float32Array(particleCount * 3);
      const colors = new Float32Array(particleCount * 3);
      const radius = 25;
      const color1 = new THREE.Color(0xf5ad42);
      const color2 = new THREE.Color(0x42c2f5);
      for (let i = 0; i < particleCount; i++) {
        const theta = 2 * Math.PI * Math.random();
        const phi = Math.acos(2 * Math.random() - 1);
        const x = radius * Math.sin(phi) * Math.cos(theta);
        const y = radius * Math.sin(phi) * Math.sin(theta);
        const z = radius * Math.cos(phi);
        positions[i * 3] = x;
        positions[i * 3 + 1] = y;
        positions[i * 3 + 2] = z;
        const chosenColor = (Math.random() < 0.5) ? color1 : color2;
        colors[i * 3] = chosenColor.r;
        colors[i * 3 + 1] = chosenColor.g;
        colors[i * 3 + 2] = chosenColor.b;
      }
      const geometry = new THREE.BufferGeometry();
      geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
      geometry.setAttribute('color', new THREE.BufferAttribute(colors, 3));
      const material = new THREE.PointsMaterial({
        size: 0.8,
        vertexColors: true,
        transparent: true,
        opacity: 0.8
      });
      const points = new THREE.Points(geometry, material);
      this.particlesGroup.add(points);
    },
    // Loads a font and creates centered 3D curved text for "PARTICIPANTI" that orbits the globe.
    addTitleAndCurve() {
      const loader = new FontLoader();
      loader.load('https://threejs.org/examples/fonts/helvetiker_regular.typeface.json', (font) => {
        const text = "PARTICIPANTI";
        const textGeometry = new TextGeometry(text, {
          font: font,
          size: 3,
          height: 1,
          curveSegments: 12,
          bevelEnabled: false
        });
        textGeometry.center();
        // Bend the geometry along an arc.
        this.bendTextGeometry(textGeometry, 30);

        const textMaterial = new THREE.MeshBasicMaterial({color: 0x000f22});
        const textMesh = new THREE.Mesh(textGeometry, textMaterial);
        // Store a reference to the text mesh for later flipping.
        this.titleMesh = textMesh;

        // Create a group for the title.
        const titleGroup = new THREE.Group();
        titleGroup.add(textMesh);
        // Initially position the group on the orbit (in the xz plane).
        titleGroup.position.set(this.titleOrbitRadius, 0, 0);
        this.titleGroup = titleGroup;
        this.scene.add(titleGroup);
      });
    },
    // Bends the text geometry so that it curves along an arc.
    bendTextGeometry(geometry, bendRadius) {
      const posAttr = geometry.attributes.position;
      const vertex = new THREE.Vector3();
      for (let i = 0; i < posAttr.count; i++) {
        vertex.fromBufferAttribute(posAttr, i);
        const angle = vertex.x / bendRadius;
        const newX = bendRadius * Math.sin(angle);
        const newZ = bendRadius * (1 - Math.cos(angle));
        vertex.x = newX;
        vertex.z += newZ;
        posAttr.setXYZ(i, vertex.x, vertex.y, vertex.z);
      }
      posAttr.needsUpdate = true;
      geometry.computeVertexNormals();
    },
    animate() {
      this.animationFrameId = requestAnimationFrame(this.animate);
      this.viewerGroup.rotation.y += 0.002;
      this.particlesGroup.rotation.y += 0.002;

      if (this.titleGroup) {
        this.titleOrbitAngle += 0.002;
        const x = this.titleOrbitRadius * Math.cos(this.titleOrbitAngle);
        const z = this.titleOrbitRadius * Math.sin(this.titleOrbitAngle);
        this.titleGroup.position.set(x, 0, z);
        // Make the title group look at the globe's center.
        this.titleGroup.lookAt(new THREE.Vector3(0, 0, 0));
        // Flip the text mesh if the title is in front of the globe (z > 0) so it's not mirrored.
        if (this.titleGroup.position.z > 0) {
          this.titleMesh.scale.x = -Math.abs(this.titleMesh.scale.x);
        } else {
          this.titleMesh.scale.x = Math.abs(this.titleMesh.scale.x);
        }
      }

      this.renderer.render(this.scene, this.camera);
    },
    simulateUsers() {
      const simulatedUsers = [];
      for (let i = 0; i < 200; i++) {
        simulatedUsers.push({
          id: i,
          user: `User ${i}`,
          picked: Math.random() < 0.2,
          eliminated: Math.random() < 0.1
        });
      }
      return simulatedUsers;
    }
  }
};
</script>

<style scoped>
.scene-container {
  width: 100%;
  height: 100%;
  min-height: 500px;
  position: relative;
}
</style>
