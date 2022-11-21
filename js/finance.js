// FORMULAS FINANCIERAS **************************************************************************************
function _irrResult (values, dates, rate) {
    const r = rate + 1
    let result = values[0]
    for (let i = 1; i < values.length; i++) {
      result += values[i] / Math.pow(r, (dates[i] - dates[0]) / 365)
    }
    return result
}

function _irrResultDeriv (values, dates, rate) {
    const r = rate + 1
    let result = 0
    for (let i = 1; i < values.length; i++) {
      const frac = (dates[i] - dates[0]) / 365
      result -= frac * values[i] / Math.pow(r, frac + 1)
    }
    return result
}

function irr (values, guess = 0.1, tol = 1e-6, maxIter = 1000) {
    const dates = []
    let positive = false
    let negative = false
    for (let i = 0; i < values.length; i++) {
      dates[i] = (i === 0) ? 0 : dates[i - 1] + 365
      if (values[i] > 0) {
        positive = true
      }
      if (values[i] < 0) {
        negative = true
      }
    }

    if (!positive || !negative) {
      return Number.NaN
    }
  
    let resultRate = guess
    let newRate, epsRate, resultValue
    let iteration = 0
    let contLoop = true

    do {
      resultValue = _irrResult(values, dates, resultRate)
      newRate = resultRate - resultValue / _irrResultDeriv(values, dates, resultRate)
      epsRate = Math.abs(newRate - resultRate)
      resultRate = newRate
      contLoop = (epsRate > tol) && (Math.abs(resultValue) > tol)
    } while (contLoop && (++iteration < maxIter))
  
    if (contLoop) {
      return Number.NaN
    }
  
    return resultRate
}

function npv (rate, values) {
    return values.reduce(
      (acc, curr, i) => acc + (curr / (1 + rate) ** i),
      0
    )
}
