({
  mainConfigFile : "scripts/config.js",
  appDir: "./",
  baseUrl: "scripts",
  removeCombined: true,
  findNestedDependencies: true,
  dir: "dist",
  optimize: "uglify2",
  optimizeCss: "standard",
  modules: [
    {
      name: "play",
      exclude: [
        "infrastructure"
      ]
    },
    {
      name: "playnote",
      exclude: [
        "infrastructure"
      ]
    },
    {
      name: "infrastructure"
    }
  ],
  generateSourceMaps: true,
  preserveLicenseComments: false
})