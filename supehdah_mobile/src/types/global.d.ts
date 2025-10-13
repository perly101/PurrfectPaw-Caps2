// This file extends the Window interface to add our custom properties
declare global {
  interface Window {
    isWebEnvironment: boolean;
  }
}

export {};