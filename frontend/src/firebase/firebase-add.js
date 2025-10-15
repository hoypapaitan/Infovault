import { db } from './index.js'
import { doc, setDoc, collection, query, where, getDocs, updateDoc } from 'firebase/firestore'
import { Loading, Notify } from 'quasar'

const addProductDocument = async (collectionName, data) => {
  return new Promise(async (resolve, reject) => {
    
    try {
        // Validate
        let q

        q = query(
          collection(db, collectionName),
          where('name', '==', data.name),
          where('capacity', '==', data.capacity),
        )

        getDocs(q).then((querySnapshot) => {
            if(querySnapshot.docs.length === 0){
              // Insert
              const docRef = doc(collection(db, collectionName))
              setDoc(docRef, data).then(() => {
                resolve()
              })
            } else {
              // Update
              const docRefUpdate = doc(db, collectionName, querySnapshot.docs[0].id)
              updateDoc(docRefUpdate, data).then(() => {
                resolve()
              })
            }
        })
    } catch (err) {
      // Loading.hide()
      Notify.create({
        type: 'negative',
        message: err.message
      })
      reject(err.message)
    }
  })
}

export default addProductDocument