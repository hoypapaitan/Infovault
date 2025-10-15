/* #version=0.0.0-1#1 rf 2023-11-07T12:14:53 400D2C82DFC836CF */
/* #version=0.0.0-1 rf 2023-11-07T12:12:07 C23ED082845F2AB6 */
import { auth, db } from './index.js'
import { createUserWithEmailAndPassword, updateProfile } from 'firebase/auth'
import { doc, setDoc, collection } from 'firebase/firestore'
import { Loading, Notify } from 'quasar'
import moment from 'moment'

const register = (data) => {
  return new Promise(async (resolve, reject) => {
    Loading.show()

    await createUserWithEmailAndPassword(
      auth,
      data.email,
      data.password,
      data.firstName,
      data.lastName
    )
      .then((userCredential) => {
        updateProfile(userCredential.user, {
          displayName: data.firstName + ' ' + data.lastName
        })

        const user = userCredential.user

        // Save user data to Firestore
        const docRef = doc(db, 'userProfile', user.uid)
        delete(data.password)
        const userData = {
          fullname: `${data.firstName} ${data.middleName} ${data.lastName}`,
          ...data
        }
        
        return setDoc(docRef, userData).then(() => {
          Loading.hide()
          resolve(userCredential.user)
        })
      })
      .catch((err) => {
        Loading.hide()
        Notify.create({
          type: 'negative',
          message: err.message
        })
        reject(err.message)
      })
  })
}
export default register

