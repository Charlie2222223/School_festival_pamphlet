import * as THREE from 'three';
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls';


// シーン設定
const scene = new THREE.Scene();

// カメラ設定
const camera = new THREE.PerspectiveCamera(
  75,
  window.innerWidth / window.innerHeight,
  0.1,
  1000
);
camera.position.set(0, 2, 10);

// レンダラー設定
const canvas = document.getElementById('myCanvas') as HTMLCanvasElement;
const renderer = new THREE.WebGLRenderer({ canvas, alpha: true });
renderer.setSize(window.innerWidth, window.innerHeight);

// コントロール設定
const controls = new OrbitControls(camera, renderer.domElement);
controls.enableDamping = true;
controls.dampingFactor = 0.05;
controls.enableZoom = true;
controls.enablePan = true;

// ライト設定
scene.add(new THREE.AmbientLight(0xffffff, 0.5));

const pointLight = new THREE.PointLight(0xffffff, 2, 100);
pointLight.position.set(10, 10, 10);
scene.add(pointLight);

// 図形と速度ベクトルの管理
const shapes: { mesh: THREE.Mesh; velocity: THREE.  Vector3 }[] = [];

for (let i = 0; i < 30; i++) {
  const shapeType = Math.random(); // 0〜1 の間の値
  let geometry: THREE.BufferGeometry;

  // 図形タイプをランダムに選択
  if (shapeType < 0.33) {
    // 丸（Torus）
    geometry = new THREE.TorusGeometry(
      Math.random() * 0.6 + 0.4,
      Math.random() * 0.1 + 0.1,
      32,
      64
    );
  } else if (shapeType < 0.66) {
    // 三角（Cone）
    geometry = new THREE.ConeGeometry(Math.random() * 0.4 + 0.2, 0.6, 6);
  } else {
    // ✅ 四角（Box）を追加
    geometry = new THREE.BoxGeometry(
      Math.random() * 0.6 + 0.4,
      Math.random() * 0.6 + 0.4,
      Math.random() * 0.6 + 0.4
    );
  }

  const baseColor = new THREE.Color(Math.random() * 0xffffff);

  const material = new THREE.MeshStandardMaterial({
    color: baseColor,
    emissive: baseColor,
    emissiveIntensity: 2.5,
    transparent: true,
    opacity: 0.5,
    metalness: 0.3,
    roughness: 0.1,
  });

  const mesh = new THREE.Mesh(geometry, material);

  // 初期位置（ランダム）
  mesh.position.set(
    (Math.random() - 0.5) * 10,
    (Math.random() - 0.5) * 10,
    (Math.random() - 0.5) * 10
  );

  // 初期速度（ゆっくり）
  const velocity = new THREE.Vector3(
    (Math.random() - 0.8) * 0.04,
    0,
    0
  );

  scene.add(mesh);
  shapes.push({ mesh, velocity });
}

// アニメーションループ
function animate() {
  requestAnimationFrame(animate);

  shapes.forEach(({ mesh, velocity }) => {
    // 回転
    mesh.rotation.x += 0.01;
    mesh.rotation.y += 0.01;

    // 移動
    mesh.position.add(velocity);

    // 壁とのバウンド（範囲: ±10）
    const axes: ("x" | "y" | "z")[] = ["x", "y", "z"];

    axes.forEach((axis) => {
      const pos = mesh.position[axis] as number;
      if (pos > 10 || pos < -10) {
        velocity[axis] = -velocity[axis];
        mesh.position[axis] = THREE.MathUtils.clamp(pos, -10, 10);
      }
    });
  });

  controls.update();
  renderer.render(scene, camera);
}
animate();

// ウィンドウリサイズ対応
window.addEventListener('resize', () => {
  camera.aspect = window.innerWidth / window.innerHeight;
  camera.updateProjectionMatrix();
  renderer.setSize(window.innerWidth, window.innerHeight);
});