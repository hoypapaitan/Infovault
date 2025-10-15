import { auth } from './index.js'
import getDetailsDocument from './firebase-get';
import { signInWithEmailAndPassword } from 'firebase/auth'
import { Loading, Notify, LocalStorage } from 'quasar'

const login = (data) => {
  return new Promise((resolve, reject) => {
    Loading.show()

    signInWithEmailAndPassword(auth, data.email, data.password).then(async userCredential => {
      const res = await getDetailsDocument(`userProfile`, userCredential.user.uid)
      LocalStorage.set('userDetails', res)
      Loading.hide()
      resolve(userCredential.user)
    }).catch(err => {
      Loading.hide()
      Notify.create({
        type: 'negative',
        message: 'Invalid Credentials please try again'
      })
      reject(err.message)
    })
  })
}

export default login
