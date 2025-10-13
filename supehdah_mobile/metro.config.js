// metro.config.js
const { getDefaultConfig } = require('expo/metro-config');

const config = getDefaultConfig(__dirname);

// Fix for 'Failed to initialize Hermes' error
config.resolver.sourceExts = process.env.RN_SRC_EXT 
  ? [...process.env.RN_SRC_EXT.split(','), ...config.resolver.sourceExts] 
  : config.resolver.sourceExts;

// Add resolver for .js files importing .ts files
config.resolver.extraNodeModules = {
  ...config.resolver.extraNodeModules,
  'react-native': require.resolve('react-native'),
  'react': require.resolve('react')
};

// Ensure all required file extensions are handled
config.resolver.sourceExts.push('mjs');
config.resolver.sourceExts.push('cjs');

module.exports = config;