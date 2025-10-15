import { db } from './index.js'
import { doc, setDoc, collection, query, where, getDocs, updateDoc, addDoc } from 'firebase/firestore'
import { Loading, Notify } from 'quasar'

const createOrderProduct = async (collectionName, data) => {
  return new Promise(async (resolve, reject) => {
    let cusInfo = data.customerInfo
    try {
      // 1st create Customer
      const newItem = await addDoc(collection(db, 'customerInfo'), cusInfo);
      const fileID = newItem.id

      const docRef = doc(collection(db, collectionName))
      await data.order.forEach(el => {
        let objpayload = {
          ...el,
          customerId: fileID,
          sellerDetails: data.salesmanDetails,
          schedule: data.scheduleInfo
        }

        setDoc(docRef, objpayload)
      });

      resolve()
    } catch (err) {
      Notify.create({
        type: 'negative',
        message: err.message
      })
      reject(err.message)
    }
  })
}

export default createOrderProduct