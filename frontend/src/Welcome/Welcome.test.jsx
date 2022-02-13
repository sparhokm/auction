import React from 'react'
import { render, screen } from '@testing-library/react'
import Welcome from './Welcome'
import { FeaturesProvider } from '../FeatureToggle'

test('renders new welcome', () => {
  render(
    <FeaturesProvider features={['WE_ARE_HERE']}>
      <Welcome />
    </FeaturesProvider>
  )

  expect(screen.queryByText(/We will be here/i)).not.toBeInTheDocument()
  expect(screen.getByText(/We are here/i)).toBeInTheDocument()
})

test('renders welcome', () => {
  render(
    <FeaturesProvider features={[]}>
      <Welcome />
    </FeaturesProvider>
  )

  expect(screen.getByText(/We will be here/i)).toBeInTheDocument()
  expect(screen.queryByText(/We are here/i)).not.toBeInTheDocument()
})
