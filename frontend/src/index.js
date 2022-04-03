import React from 'react'
import { createRoot } from 'react-dom/client'
import './index.css'
import App from './App'
import cookie from 'cookie'
import { mergeFeatures } from './FeatureToggle'
import defaultFeatures from './features'

const cookies = cookie.parse(document.cookie)
const cookieFeatures = (cookies.features || '')
  .split(/\s*,\s*/g)
  .filter(Boolean)

const features = mergeFeatures(defaultFeatures, cookieFeatures)

const root = createRoot(document.getElementById('root'))
root.render(
  <React.StrictMode>
    <App features={features} />
  </React.StrictMode>
)
