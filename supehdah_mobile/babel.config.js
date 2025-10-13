module.exports = function(api) {
  api.cache(true);
  
  const isWeb = process.env.PLATFORM === 'web';
  
  return {
    presets: ['babel-preset-expo'],
    plugins: [
      // Web-specific plugins
      ...(isWeb ? [
        './react-navigation-web-patch.js' // Apply our patch only in web environment
      ] : [])
    ]
  };
};