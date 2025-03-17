// src/types/textgeometry.d.ts
declare module 'three/examples/jsm/loaders/FontLoader' {
    import { Loader } from 'three';
    import { Shape } from 'three';
  
    export class Font {
      data: any;
      generateShapes(text: string, size: number): Shape[];
    }
  
    export class FontLoader extends Loader {
      load(
        url: string,
        onLoad: (font: Font) => void,
        onProgress?: (event: ProgressEvent) => void,
        onError?: (event: ErrorEvent) => void
      ): void;
    }
  }